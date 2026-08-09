const canvas            = document.getElementById('previewCanvas');
const videoBox          = document.getElementById('videoBox');
const stage             = document.querySelector('.canvas-wrapper');
const ctx               = canvas.getContext('2d');
const captureBtn        = document.getElementById('captureBtn');
const rotSlider         = document.getElementById('stickerRotation');
const sizeSlider        = document.getElementById('stickerSize');
const rotValue          = document.getElementById('rotationValue');
const sizeValue         = document.getElementById('sizeValue');
const deleteStickerBtn  = document.getElementById('deleteStickerBtn');

// -- State --

let videoStream         = null;
let sourceImage         = null; // HTMLVideoElement ou HTMLImageElement
let stickerImg          = null;
let selectedStickerId   = null;

let stickers = [];
let activeSticker = null;

let isDragging = false;
let dragOffset = { x: 0, y: 0 };

// -- Webcam --

function resizeCanvas() {
    console.log('canvas resized');
    const isMobile = window.innerWidth <= 767;

    const stageW = stage.clientWidth;
    const stageH = stage.clientHeight;

    let w, h;

    if (isMobile) {
        // Portrait 9:16 — la hauteur est contraignante
        h = stageH;
        w = Math.round(h * 9 / 16);
        if (w > stageW) {
            w = stageW;
            h = Math.round(w * 16 / 9);
        }
    } else {
        // Paysage 16:9 — prend la dimension qui rentre sans déborder
        const hFromWidth  = Math.round(stageW * 9 / 16);
        if (hFromWidth <= stageH) {
            w = stageW;
            h = hFromWidth;
        } else {
            h = stageH;
            w = Math.round(h * 16 / 9);
        }
    }

    videoBox.style.width  = w + 'px';
    videoBox.style.height = h + 'px';

    canvas.width  = w;
    canvas.height = h;

    // console.log('Canvas resized to:', canvas.width, canvas.height);
}

window.addEventListener('resize', () => {
    resizeCanvas();
    if (sourceImage && !(sourceImage instanceof HTMLVideoElement))
        renderLoop();
});

async function startWebcam() {
    try {
        const stream = await navigator.mediaDevices.getUserMedia({ video: true });
        videoStream = stream;

        const video = document.createElement('video');
        video.srcObject = stream;
        video.autoplay = true;
        video.playsInline = true;

        sourceImage = video;

        video.addEventListener('loadedmetadata', () => {
            resizeCanvas();
            renderLoop();
        });
    } catch (err) {
        document.getElementById('fallbackSection').style.display = 'flex';
    }
}

// -- Fallback --

document.getElementById('photoInput').addEventListener('change', (e) => {
    const file = e.target.files[0];
    if (!file)
        return ;
    const img = new Image();
    img.onload = () => {
        canvas.width = img.width;
        canvas.height = img.height;
        sourceImage = img;
        renderLoop();
    }
    img.src = URL.createObjectURL(file);
})

// -- Render loop --

function drawCover(context, image) {
    const imageRatio = image.videoWidth / image.videoHeight;
    const canvasRatio = canvas.width / canvas.height;

    let sx, sy, sWidth, sHeight;
    if (imageRatio > canvasRatio) {
        sHeight = image.videoHeight;
        sWidth = sHeight * canvasRatio;

        sx = (image.videoWidth - sWidth) / 2;
        sy = 0;
    } else {
        sWidth = image.videoWidth;
        sHeight = sWidth / canvasRatio;

        sx = 0;
        sy = (image.videoHeight - sHeight) / 2;
    }

    context.save();
    context.translate(canvas.width, 0);
    context.scale(-1, 1); // effet miroir
    context.drawImage(image, sx, sy, sWidth, sHeight, 0, 0, canvas.width, canvas.height);
    context.restore();
}

function renderLoop() {
    ctx.clearRect(0, 0, canvas.width, canvas.height);

    if (sourceImage instanceof HTMLVideoElement) {
        drawCover(ctx, sourceImage);
    } else if (sourceImage instanceof HTMLImageElement) {
        ctx.drawImage(sourceImage, 0, 0, canvas.width, canvas.height);
    }

    stickers.forEach(sticker => {
        ctx.save();

        ctx.translate(
            sticker.x + sticker.w / 2,
            sticker.y + sticker.h / 2
        );
        ctx.rotate((sticker.angle * Math.PI) / 180);

        ctx.drawImage(
            sticker.img,
            -sticker.w / 2,
            -sticker.h / 2,
            sticker.w,
            sticker.h
        );
        ctx.restore();
    });
    if (sourceImage instanceof HTMLVideoElement)
        requestAnimationFrame(renderLoop);
}

// -- Sticker Selection --

document.querySelectorAll('.sticker-thumb').forEach(thumb => {
    console.log("Sticker" + thumb.dataset.id + "treated");
    thumb.addEventListener('click', () => {
        console.log('sticker Selected');
        document.querySelectorAll('sticker-thumb').forEach(t => t.classList.remove('selected'));
        thumb.classList.add('selected');

        selectedStickerId = thumb.dataset.id

        const img = new Image();
        img.onload = () => {
            const ratio = img.width / img.height;

            const maxSize = canvas.width * 0.25

            const w = ratio >= 1 ? maxSize : maxSize * ratio;
            const h = ratio < 1 ? maxSize : maxSize / ratio;

            const x = (canvas.width - w) / 2;
            const y = (canvas.height - h) / 2;

            const newSticker = {
                img: img,
                stickerId: selectedStickerId,

                x: x,
                y: y,
                w: w,
                h: h,
                angle: 0,
                ratio: ratio
            }

            stickers.push(newSticker);
            activeSticker = newSticker;
            updateStickerControls();

            captureBtn.disabled = stickers.length === 0;

            if (!(sourceImage instanceof HTMLVideoElement))
                renderLoop();
        };
        img.src = thumb.dataset.path;
        resizeCanvas();
        captureBtn.disabled = false;
    })
});

// -- Sliders --

rotSlider.addEventListener('input', () => {
    activeSticker.angle = parseFloat(rotSlider.value);
    rotValue.textContent = activeSticker.angle + '°';
    if (!(sourceImage instanceof HTMLVideoElement))
        renderLoop();
});

sizeSlider. addEventListener('input', () => {
    const newWidth = parseInt(sizeSlider.value);

    const cx = activeSticker.x + activeSticker.w / 2;
    const cy = activeSticker.y + activeSticker.h / 2;

    activeSticker.w = newWidth;
    activeSticker.h = newWidth / activeSticker.ratio;

    activeSticker.x = cx - activeSticker.w / 2;
    activeSticker.y = cy - activeSticker.h / 2;

    sizeValue.textContent = newWidth + 'px';

    if (!(sourceImage instanceof HTMLVideoElement))
        renderLoop();
})

function getCanvasPos(e) {
    const rect = canvas.getBoundingClientRect()

    const scaleX = canvas.width / rect.width;
    const scaleY = canvas.height / rect.height;

    const clientX = e.touches ? e.touches[0].clientX : e.clientX;
    const clientY = e.touches ? e.touches[0].clientY : e.clientY;

    return {
        x: (clientX - rect.left) * scaleX,
        y: (clientY - rect.top) * scaleY
    };
}

function getStickerAtPosition(pos) {

    for (let i = stickers.length - 1; i >= 0; i--) {

        const sticker = stickers[i];

        if (
            pos.x >= sticker.x &&
            pos.x <= sticker.x + sticker.w &&
            pos.y >= sticker.y &&
            pos.y <= sticker.y + sticker.h
        ) {
            return sticker;
        }
    }

    return null;
}

canvas.addEventListener('mousedown', startDrag);
canvas.addEventListener('touchstart', startDrag, { passive: true });

function updateStickerControls() {
    if (!activeSticker) {
        rotSlider.value = 0;
        rotValue.textContent = '0°';

        sizeSlider.value = 0;
        sizeValue.textContent = '0px';

        deleteStickerBtn.classList.add('d-none');
        return ;
    }
    rotSlider.value = activeSticker.angle;
    rotValue.textContent = activeSticker.angle + '°';

    sizeSlider.value = Math.round(activeSticker.w);
    sizeValue.textContent = Math.round(activeSticker.w) + 'px';
    deleteStickerBtn.classList.remove('d-none');
}

function startDrag(e) {

    const pos = getCanvasPos(e);

    const sticker = getStickerAtPosition(pos);

    if (sticker) {

        activeSticker = sticker;

        isDragging = true;

        dragOffset.x = pos.x - sticker.x;
        dragOffset.y = pos.y - sticker.y;
    }
    updateStickerControls();
}

canvas.addEventListener('mousemove', moveDrag);
canvas.addEventListener('touchmove', moveDrag, { passive: true });

function moveDrag(e) {
    if (!isDragging || !activeSticker)
        return ;

    const pos = getCanvasPos(e);

    activeSticker.x = pos.x - dragOffset.x;
    activeSticker.y = pos.y - dragOffset.y;

    if (!(sourceImage instanceof HTMLVideoElement))
        renderLoop();
}

canvas.addEventListener('mouseup', endDrag);
canvas.addEventListener('touchend', endDrag);
canvas.addEventListener('mouseleave', endDrag);

function endDrag() {
    isDragging = false;
    // activeSticker = null;
}

deleteStickerBtn.addEventListener('click', () => {
    if (!activeSticker)
        return ;

    const index = stickers.indexOf(activeSticker);
    if (index !== -1) {
        stickers.splice(index, 1);
    }
    activeSticker = null;

    updateStickerControls();
    captureBtn.disabled = stickers.length === 0;

    if (!(sourceImage instanceof HTMLVideoElement))
        renderLoop();
});

captureBtn.addEventListener('click', () => {
    if (stickers.length === 0)
        return ;

    const captureCanvas = document.createElement('canvas');

    captureCanvas.width = canvas.width;
    captureCanvas.height = canvas.height;

    const captureCtx = captureCanvas.getContext('2d');

    if (sourceImage instanceof HTMLVideoElement)
        drawCover(captureCtx, sourceImage);
    else if (sourceImage instanceof HTMLImageElement)
        captureCtx.drawImage(sourceImage, 0, 0, captureCanvas.width, captureCanvas.height);

    document.getElementById('imageData').value = captureCanvas.toDataURL('image/jpeg', 0.9);
    document.getElementById('stickersData').value =
        JSON.stringify(
            stickers.map(sticker => ({
                id: sticker.stickerId,
                x: Math.round(sticker.x),
                y: Math.round(sticker.y),
                w: Math.round(sticker.w),
                h: Math.round(sticker.h),
                angle: sticker.angle
            }))
        );
    document.getElementById('createForm').submit();
});

resizeCanvas();
startWebcam();
updateStickerControls();
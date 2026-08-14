import { canvas, videoBox, stage, ctx, state } from './state.js';

export function resizeCanvas() {
    console.log('canvas resized');
    const isMobile = window.innerWidth <= 767;

    const stageW = stage.clientWidth;
    const stageH = stage.clientHeight;

    let w, h;

    if (isMobile) {
        // Portrait 9:16
        h = stageH;
        w = Math.round(h * 9 / 16);
        if (w > stageW) {
            w = stageW;
            h = Math.round(w * 16 / 9);
        }
    } else {
        // Paysage 16:9
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

}

export function drawCover(context, image, { mirror = false, fit = 'cover' } = {}) {
    const srcWidth  = image.videoWidth  ?? image.naturalWidth;
    const srcHeight = image.videoHeight ?? image.naturalHeight;

    const imageRatio = srcWidth / srcHeight;
    const canvasRatio = canvas.width / canvas.height;

    context.save();
    if (mirror) {
        context.translate(canvas.width, 0);
        context.scale(-1, 1); // effet miroir (webcam uniquement)
    }

    if (fit === 'contain')
        cropPicture(context, image, srcWidth, srcHeight, imageRatio, canvasRatio);
    else
        framePicture(context, image, srcWidth, srcHeight, imageRatio, canvasRatio);

    context.restore();
}

export function renderLoop() {
    ctx.clearRect(0, 0, canvas.width, canvas.height);

    if (state.sourceImage instanceof HTMLVideoElement) {
        drawCover(ctx, state.sourceImage, { mirror: true });
    } else if (state.sourceImage instanceof HTMLImageElement) {
        drawCover(ctx, state.sourceImage, { fit: 'contain' });
    }

    state.stickers.forEach(sticker => {
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
    if (state.sourceImage instanceof HTMLVideoElement)
        requestAnimationFrame(renderLoop);
}

function cropPicture(context, image, srcWidth, srcHeight, imageRatio, canvasRatio) {
    let dWidth, dHeight;
    if (imageRatio > canvasRatio) {
        dWidth = canvas.width;
        dHeight = dWidth / imageRatio;
    } else {
        dHeight = canvas.height;
        dWidth = dHeight * imageRatio;
    }
    const dx = (canvas.width - dWidth) / 2;
    const dy = (canvas.height - dHeight) / 2;

    context.fillStyle = '#000';
    context.fillRect(0, 0, canvas.width, canvas.height);
    context.drawImage(image, 0, 0, srcWidth, srcHeight, dx, dy, dWidth, dHeight);
}

function framePicture(context, image, srcWidth, srcHeight, imageRatio, canvasRatio) {
    let sx, sy, sWidth, sHeight;
    if (imageRatio > canvasRatio) {
        sHeight = srcHeight;
        sWidth = sHeight * canvasRatio;

        sx = (srcWidth - sWidth) / 2;
        sy = 0;
    } else {
        sWidth = srcWidth;
        sHeight = sWidth / canvasRatio;

        sx = 0;
        sy = (srcHeight - sHeight) / 2;
    }
    context.drawImage(image, sx, sy, sWidth, sHeight, 0, 0, canvas.width, canvas.height);
}

window.addEventListener('resize', () => {
    resizeCanvas();
    if (state.sourceImage && !(state.sourceImage instanceof HTMLVideoElement))
        renderLoop();
});
import { canvas, dom, state } from './state.js';
import { resizeCanvas, renderLoop } from './canvas.js';

export function updateStickerControls() {
    if (!state.activeSticker) {
        dom.rotSlider.value = 0;
        dom.rotValue.textContent = '0°';

        dom.sizeSlider.value = 0;
        dom.sizeValue.textContent = '0px';

        dom.deleteStickerBtn.classList.add('d-none');
        return ;
    }
    dom.rotSlider.value = state.activeSticker.angle;
    dom.rotValue.textContent = state.activeSticker.angle + '°';

    dom.sizeSlider.value = Math.round(state.activeSticker.w);
    dom.sizeValue.textContent = Math.round(state.activeSticker.w) + 'px';
    dom.deleteStickerBtn.classList.remove('d-none');
}

document.querySelectorAll('.sticker-thumb').forEach(thumb => {
    console.log("Sticker" + thumb.dataset.id + "treated");
    thumb.addEventListener('click', () => {
        console.log('sticker Selected');
        document.querySelectorAll('sticker-thumb').forEach(t => t.classList.remove('selected'));
        thumb.classList.add('selected');

        state.selectedStickerId = thumb.dataset.id

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
                stickerId: state.selectedStickerId,

                x: x,
                y: y,
                w: w,
                h: h,
                angle: 0,
                ratio: ratio
            }

            state.stickers.push(newSticker);
            state.activeSticker = newSticker;
            updateStickerControls();

            dom.captureBtn.disabled = state.stickers.length === 0;

            if (!(state.sourceImage instanceof HTMLVideoElement))
                renderLoop();
        };
        img.src = thumb.dataset.path;
        resizeCanvas();
        dom.captureBtn.disabled = false;
    })
});

dom.rotSlider.addEventListener('input', () => {
    if(state.activeSticker) {
        state.activeSticker.angle = parseFloat(dom.rotSlider.value);
        dom.rotValue.textContent = state.activeSticker.angle + '°';
        if (!(state.sourceImage instanceof HTMLVideoElement))
            renderLoop();
    }
});

dom.sizeSlider. addEventListener('input', () => {
    if(state.activeSticker) {
        const newWidth = parseInt(dom.sizeSlider.value);

        const cx = state.activeSticker.x + state.activeSticker.w / 2;
        const cy = state.activeSticker.y + state.activeSticker.h / 2;

        state.activeSticker.w = newWidth;
        state.activeSticker.h = newWidth / state.activeSticker.ratio;

        state.activeSticker.x = cx - state.activeSticker.w / 2;
        state.activeSticker.y = cy - state.activeSticker.h / 2;

        dom.sizeValue.textContent = newWidth + 'px';
   
    }
    if (!(state.sourceImage instanceof HTMLVideoElement))
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

    for (let i = state.stickers.length - 1; i >= 0; i--) {

        const sticker = state.stickers[i];

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

function startDrag(e) {

    const pos = getCanvasPos(e);

    const sticker = getStickerAtPosition(pos);

    if (sticker) {

        state.activeSticker = sticker;

        state.isDragging = true;

        state.dragOffset.x = pos.x - sticker.x;
        state.dragOffset.y = pos.y - sticker.y;
    }
    updateStickerControls();
}

function moveDrag(e) {
    if (!state.isDragging || !state.activeSticker)
        return ;

    const pos = getCanvasPos(e);

    state.activeSticker.x = pos.x - state.dragOffset.x;
    state.activeSticker.y = pos.y - state.dragOffset.y;

    if (!(state.sourceImage instanceof HTMLVideoElement))
        renderLoop();
}

function endDrag() {
    state.isDragging = false;
}

canvas.addEventListener('mousedown', startDrag);
canvas.addEventListener('touchstart', startDrag, { passive: true });
canvas.addEventListener('mousemove', moveDrag);
canvas.addEventListener('touchmove', moveDrag, { passive: true });
canvas.addEventListener('mouseup', endDrag);
canvas.addEventListener('touchend', endDrag);
canvas.addEventListener('mouseleave', endDrag);

dom.deleteStickerBtn.addEventListener('click', () => {
    if (!state.activeSticker)
        return ;

    const index = state.stickers.indexOf(state.activeSticker);
    if (index !== -1) {
        state.stickers.splice(index, 1);
    }
    state.activeSticker = null;

    updateStickerControls();
    dom.captureBtn.disabled = state.stickers.length === 0;

    if (!(state.sourceImage instanceof HTMLVideoElement))
        renderLoop();
});
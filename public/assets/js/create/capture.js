import { canvas, dom, state } from './state.js';
import { drawCover } from './canvas.js';

dom.captureBtn.addEventListener('click', () => {
    if (state.stickers.length === 0)
        return ;

    const captureCanvas = document.createElement('canvas');

    captureCanvas.width = canvas.width;
    captureCanvas.height = canvas.height;

    const captureCtx = captureCanvas.getContext('2d');

    if (state.sourceImage instanceof HTMLVideoElement)
        drawCover(captureCtx, state.sourceImage, { mirror: true });
    else if (state.sourceImage instanceof HTMLImageElement)
        drawCover(captureCtx, state.sourceImage, { fit: 'contain' });

    document.getElementById('imageData').value = captureCanvas.toDataURL('image/jpeg', 0.9);
    document.getElementById('stickersData').value =
        JSON.stringify(
            state.stickers.map(sticker => ({
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
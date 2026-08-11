import { canvas, videoBox, stage, ctx, state } from './state.js';

export function resizeCanvas() {
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

export function drawCover(context, image) {
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

export function renderLoop() {
    ctx.clearRect(0, 0, canvas.width, canvas.height);

    if (state.sourceImage instanceof HTMLVideoElement) {
        drawCover(ctx, state.sourceImage);
    } else if (state.sourceImage instanceof HTMLImageElement) {
        ctx.drawImage(state.sourceImage, 0, 0, canvas.width, canvas.height);
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

window.addEventListener('resize', () => {
    resizeCanvas();
    if (state.sourceImage && !(state.sourceImage instanceof HTMLVideoElement))
        renderLoop();
});
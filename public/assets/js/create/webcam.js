import { canvas, state } from './state.js';
import { resizeCanvas, renderLoop } from './canvas.js';

export async function startWebcam() {
    try {
        const stream = await navigator.mediaDevices.getUserMedia({ video: true });
        state.videoStream = stream;

        const video = document.createElement('video');
        video.srcObject = stream;
        video.autoplay = true;
        video.playsInline = true;

        state.sourceImage = video;

        video.addEventListener('loadedmetadata', () => {
            resizeCanvas();
            renderLoop();
        });
    } catch (err) {
        document.getElementById('fallbackSection').style.display = 'flex';
    }
}

document.getElementById('photoInput').addEventListener('change', (e) => {
    console.log("Choose a picture");
    const file = e.target.files[0];
    if (!file)
        return ;
    const img = new Image();
    img.onload = () => {
        resizeCanvas();
        state.sourceImage = img;
        renderLoop();
        document.getElementById('fallbackSection').style.display = 'none';
    }
    img.src = URL.createObjectURL(file);
})
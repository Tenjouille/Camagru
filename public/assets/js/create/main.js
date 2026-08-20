import { resizeCanvas } from './canvas.js';
import { startWebcam } from './webcam.js';
import { updateStickerControls } from './stickers.js';
import './capture.js';
import './posts.js';

resizeCanvas();
startWebcam();
updateStickerControls();
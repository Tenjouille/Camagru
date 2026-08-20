export const canvas            = document.getElementById('previewCanvas');
export const videoBox          = document.getElementById('videoBox');
export const stage             = document.querySelector('.canvas-wrapper');
export const ctx               = canvas.getContext('2d');

export const dom = {
    captureBtn:       document.getElementById('captureBtn'),
    rotSlider:        document.getElementById('stickerRotation'),
    sizeSlider:        document.getElementById('stickerSize'),
    rotValue:         document.getElementById('rotationValue'),
    sizeValue:        document.getElementById('sizeValue'),
    deleteStickerBtn: document.getElementById('deleteStickerBtn')
};

export const myCreationSidebar = document.querySelector('#myCreationSidebar');

export const state = {
    videoStream: null,
    sourceImage: null, // HTMLVideoElement ou HTMLImageElement
    selectedStickerId: null,
    stickers: [],
    activeSticker: null,
    isDragging: false,
    dragOffset: { x: 0, y: 0 }
};
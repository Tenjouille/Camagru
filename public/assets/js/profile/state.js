// Ce module n'est chargé que par profile.php, qui rend toujours ces
// éléments — pas besoin de garde `if`, contrairement à l'ancien main.js
// global qui devait vérifier leur présence sur chaque page.
export const pseudoForm     = document.querySelector('.pseudo-form');
export const emailForm      = document.querySelector('.email-form');
export const passwordForm   = document.querySelector('.pwd-form');
export const notifForm      = document.querySelector('.notif-form');
export const stickerForm    = document.querySelector('.sticker-form');
export const myStickersGrid = document.querySelector('#myStickersGrid');
export const headerPseudo   = document.querySelector('#headerPseudo');

const   toggler = document.querySelector('.navbar-toggler');
const   navMenu = document.querySelector('#mainNav');

toggler.addEventListener('click', () => {
    navMenu.classList.toggle('show');
});
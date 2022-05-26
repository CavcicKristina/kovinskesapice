const navSlide = () => {
    const burger = document.querySelector('.burger');
    const nav = document.querySelector('.mainMenu');
    const navLinks = document.querySelectorAll('.mainMenu li');
    // toggle navigation
    burger.addEventListener('click', () => {
        nav.classList.toggle('nav-active');
    });
    // animate links
    navLinks.forEach((link,index) => {
        link.style.animation = `navLinkFade 0.5s ease forwards ${index / 7 + 2.5}s`;
    });
}

navSlide();
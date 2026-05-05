document.addEventListener("DOMContentLoaded", function () {

    const toggle = document.querySelector(".toggle-cats");
    const block = document.querySelector(".categories-block");

    if (toggle && block) {
        toggle.addEventListener("click", function () {
            block.classList.toggle("active");
            toggle.classList.toggle("active");
        });
    }

});
//СОХРАНЕНИЕ СКРОЛЛА
    const forms = document.querySelectorAll("form");

    forms.forEach(form => {
        form.addEventListener("submit", () => {
            localStorage.setItem("scrollY", window.scrollY);
        });
    });

window.addEventListener("load", () => {
    const scrollY = localStorage.getItem("scrollY");

    if (scrollY !== null) {
        window.scrollTo(0, parseInt(scrollY));
        localStorage.removeItem("scrollY");
    }
});
// открываюшийся список
document.querySelectorAll('.dropdown').forEach(drop => {
    const btn = drop.querySelector('.dropbtn');
    const menu = drop.querySelector('.dropdown-content');

    btn.addEventListener('click', (e) => {
        e.stopPropagation();
        menu.classList.toggle('show');
    });
});

document.addEventListener('click', () => {
    document.querySelectorAll('.dropdown-content').forEach(menu => {
        menu.classList.remove('show');
    });
});
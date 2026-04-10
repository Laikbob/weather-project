document.addEventListener("DOMContentLoaded", function() {
    const toggle = document.querySelector(".toggle-cats");
    const block = document.querySelector(".categories-block");

    toggle.addEventListener("click", function() {
        block.classList.toggle("active");  // раскрытие/скрытие блока
        toggle.classList.toggle("active"); // поворот стрелки
    });
});


// сохраняем позицию перед отправкой формы
document.querySelectorAll("form").forEach(form => {
    form.addEventListener("submit", () => {
        localStorage.setItem("scrollY", window.scrollY);
    });
});

// восстанавливаем после загрузки
window.addEventListener("load", () => {
    const scrollY = localStorage.getItem("scrollY");
    if (scrollY !== null) {
        window.scrollTo(0, scrollY);
        localStorage.removeItem("scrollY");
    }
});
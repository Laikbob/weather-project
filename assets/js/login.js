// ЧАСЫ
function updateClock() {
    const now = new Date();

    const h = String(now.getHours()).padStart(2, '0');
    const m = String(now.getMinutes()).padStart(2, '0');
    const s = String(now.getSeconds()).padStart(2, '0');

    document.getElementById("clock").innerText = `${h}:${m}:${s}`;
}

setInterval(updateClock, 1000);
updateClock();

function togglePass() {
    const input = document.getElementById("password");

    if (input.type === "password") {
        input.type = "text";
    } else {
        input.type = "password";
    }
}

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
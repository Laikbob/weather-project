// открываюшийся список
document.querySelectorAll('.dropdown').forEach(drop => {
    const btn = drop.querySelector('.dropbtn');
    const menu = drop.querySelector('.dropdown-content');

    btn.addEventListener('click', (e) => {
        e.stopPropagation();

        // закрыть другие
        document.querySelectorAll('.dropdown-content').forEach(m => {
            if (m !== menu) m.classList.remove('show');
        });

        menu.classList.toggle('show');
    });
});

document.addEventListener('click', () => {
    document.querySelectorAll('.dropdown-content').forEach(menu => {
        menu.classList.remove('show');
    });
});
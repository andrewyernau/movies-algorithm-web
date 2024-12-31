document.addEventListener('DOMContentLoaded', () => {
    const userInfo = document.querySelector('.user-info');
    const dropdownMenu = document.querySelector('.user-info .dropdown-menu');

    if (!userInfo || !dropdownMenu) {
        console.warn('Unexpected error.');
        return;
    }

    userInfo.addEventListener('click', (event) => {
        event.stopPropagation();
        dropdownMenu.classList.toggle('active');
    });

    document.addEventListener('click', () => {
        dropdownMenu.classList.remove('active');
    });

    dropdownMenu.addEventListener('click', (event) => {
        event.stopPropagation();
    });
});

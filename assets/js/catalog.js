document.addEventListener('DOMContentLoaded', () => {
    const dropdownButton = document.getElementById('dropdown-button');
    const dropdownList = document.getElementById('dropdown-list');
    const dropdownArrow = document.getElementById('dropdown-arrow');
    const generoInput = document.getElementById('genero-input');

    dropdownButton.addEventListener('click', () => {
        dropdownList.classList.toggle('hidden');
        dropdownArrow.textContent = dropdownList.classList.contains('hidden') ? '▼' : '▲';
    });

    dropdownList.addEventListener('click', (event) => {
        if (event.target && event.target.matches('div[data-id]')) {
            const genreId = event.target.getAttribute('data-id');
            generoInput.value = genreId;
            dropdownList.classList.add('hidden');
            dropdownArrow.textContent = '▼';
            document.querySelector('form').submit();
        }
    });

    // Cerrar dropdown
    document.addEventListener('click', (event) => {
        if (!dropdownButton.contains(event.target) && !dropdownList.contains(event.target)) {
            dropdownList.classList.add('hidden');
            dropdownArrow.textContent = '▼';
        }
    });
});

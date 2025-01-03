document.addEventListener("DOMContentLoaded", () => {
    const dropdownToggle = document.querySelector("#genres-dropdown > div");
    const dropdownMenu = document.querySelector(".dropdown-menu-movies");

    if (dropdownToggle && dropdownMenu) {
        // Abrir/cerrar el menú de géneros
        dropdownToggle.addEventListener("click", () => {
            const isVisible = dropdownMenu.style.display === "block";
            dropdownMenu.style.display = isVisible ? "none" : "block";
        });

        // Cerrar el menú automáticamente al seleccionar un género
        const dropdownOptions = dropdownMenu.querySelectorAll("a");
        dropdownOptions.forEach(option => {
            option.addEventListener("click", () => {
                dropdownMenu.style.display = "none";
            });
        });
    }
});

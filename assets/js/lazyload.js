 // Lazy Loading: https://developer.mozilla.org/en-US/docs/Web/Performance/Lazy_loading
 document.addEventListener("DOMContentLoaded", () => {
    const lazyImages = document.querySelectorAll(".lazy-image");

    // Crear un Intersection Observer para cargar imágenes solo cuando estén cerca del viewport
    const imageObserver = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.dataset.src; // Asigna la URL de la imagen real
                img.classList.remove("lazy-image"); // Elimina la clase para que no se vuelva a observar
                imageObserver.unobserve(img); // Deja de observar la imagen
            }
        });
    });

    // Observar todas las imágenes con la clase "lazy-image"
    lazyImages.forEach((img) => imageObserver.observe(img));
});

//https://developer.mozilla.org/en-US/docs/Web/API/Intersection_Observer_API
document.addEventListener("DOMContentLoaded", () => {
    // Verificamos si ya existen las tarjetas de película antes de continuar
    const checkMovieCardsExist = () => {
        const movieCards = document.querySelectorAll(".movie-card");
        
        // Si no hay movieCards, esperamos un poco y verificamos de nuevo
        if (movieCards.length === 0) {
            console.log("Esperando a que se carguen las tarjetas...");
            setTimeout(checkMovieCardsExist, 100); // Reintentar después de 100 ms
            return;
        }
        
        // Si ya existen las movieCards, continuar con el código de carrusel
        initializeCarousel(movieCards);
    };

    // Función que inicializa el carrusel
    const initializeCarousel = (movieCards) => {
        const carousel = document.querySelector(".carousel");
        const leftBtn = document.querySelector(".left-btn");
        const rightBtn = document.querySelector(".right-btn");

        const cardWidth = movieCards[0].offsetWidth;
        const gap = parseInt(getComputedStyle(carousel).gap) || 0;
        const visibleCards = 4;
        const scrollStep = (cardWidth + gap) * visibleCards;
        let scrollAmount = 0;
        const maxScroll = carousel.scrollWidth - carousel.clientWidth;

        // Manejadores de eventos para los botones de navegación
        leftBtn.addEventListener("click", () => {
            scrollAmount -= scrollStep;
            if (scrollAmount < 0) scrollAmount = 0;
            carousel.style.transform = `translateX(-${scrollAmount}px)`;
        });

        rightBtn.addEventListener("click", () => {
            scrollAmount += scrollStep;
            if (scrollAmount > maxScroll) scrollAmount = maxScroll;
            carousel.style.transform = `translateX(-${scrollAmount}px)`;
        });

        // Animación de las tarjetas cuando aparecen en el viewport
        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add("animate");
                    observer.unobserve(entry.target);
                }
            });
        });

        // Observar todas las tarjetas del carrusel
        movieCards.forEach((card) => observer.observe(card));
    };

    // Verificar si las movieCards están presentes
    checkMovieCardsExist();
});

//https://developer.mozilla.org/en-US/docs/Web/API/Intersection_Observer_API
 // Lazy Loading: https://developer.mozilla.org/en-US/docs/Web/Performance/Lazy_loading
 document.addEventListener("DOMContentLoaded", () => {
    const carousel = document.querySelector(".carousel");
    const leftBtn = document.querySelector(".left-btn");
    const rightBtn = document.querySelector(".right-btn");
    const movieCards = document.querySelectorAll(".movie-card");
    const cardWidth = movieCards[0].offsetWidth;
    const gap = parseInt(getComputedStyle(carousel).gap) || 0;
    const visibleCards = 3;
    const scrollStep = (cardWidth + gap) * visibleCards;
    let scrollAmount = 0;
    const maxScroll = carousel.scrollWidth - carousel.clientWidth;

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
    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                entry.target.classList.add("animate");
                observer.unobserve(entry.target);
            }
        });
    });
    movieCards.forEach((card) => observer.observe(card));

    const lazyImages = document.querySelectorAll(".lazy-image");

    const imageObserver = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.dataset.src;
                img.classList.remove("lazy-image");
                imageObserver.unobserve(img);
            }
        });
    });

    lazyImages.forEach((img) => imageObserver.observe(img));
});


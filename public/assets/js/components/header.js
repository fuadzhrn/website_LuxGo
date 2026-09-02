document.addEventListener("DOMContentLoaded", () => {
    const header = document.querySelector(".site-header");

    if (!header) {
        return;
    }

    const SCROLL_THRESHOLD = 24;
    let ticking = false;

    const updateHeaderState = () => {
        header.classList.toggle("is-scrolled", window.scrollY > SCROLL_THRESHOLD);
        ticking = false;
    };

    const requestHeaderUpdate = () => {
        if (ticking) {
            return;
        }

        window.requestAnimationFrame(updateHeaderState);
        ticking = true;
    };

    updateHeaderState();
    window.addEventListener("scroll", requestHeaderUpdate, { passive: true });
});

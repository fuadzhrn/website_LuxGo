(function () {
    "use strict";

    const root = document.documentElement;

    const revealAll = (elements) => {
        elements.forEach((element) => {
            element.classList.add("is-visible");
        });
    };

    const init = () => {
        const elements = document.querySelectorAll("[data-reveal]");

        if (!elements.length) {
            return;
        }

        const prefersReducedMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;

        if (prefersReducedMotion || !("IntersectionObserver" in window)) {
            revealAll(elements);
            return;
        }

        const observer = new IntersectionObserver(
            (entries) => {
                entries.forEach((entry) => {
                    if (!entry.isIntersecting) {
                        return;
                    }

                    entry.target.classList.add("is-visible");
                    observer.unobserve(entry.target);
                });
            },
            {
                rootMargin: "0px 0px -10% 0px",
                threshold: 0.15,
            }
        );

        elements.forEach((element) => {
            observer.observe(element);
        });
    };

    root.classList.add("has-motion");

    if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", init);
    } else {
        init();
    }
})();

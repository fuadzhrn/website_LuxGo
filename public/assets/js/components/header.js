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

document.addEventListener("DOMContentLoaded", () => {
    const header = document.querySelector(".site-header");
    const toggle = document.querySelector("[data-menu-toggle]");
    const menu = document.querySelector("[data-site-menu]");

    if (!header || !toggle || !menu) {
        return;
    }

    const iconOpen = toggle.querySelector('[data-menu-icon="open"]');
    const iconClose = toggle.querySelector('[data-menu-icon="close"]');
    const desktop = window.matchMedia("(min-width: 1024px)");

    const setMenu = (isOpen) => {
        menu.classList.toggle("is-open", isOpen);
        header.classList.toggle("is-menu-open", isOpen);
        document.body.classList.toggle("has-menu-open", isOpen);

        toggle.setAttribute("aria-expanded", String(isOpen));
        /* Labels come from the markup so they follow the active locale. */
        toggle.setAttribute(
            "aria-label",
            isOpen
                ? toggle.dataset.labelClose || "Close menu"
                : toggle.dataset.labelOpen || "Open menu"
        );

        if (iconOpen) {
            iconOpen.hidden = isOpen;
        }

        if (iconClose) {
            iconClose.hidden = !isOpen;
        }
    };

    const isOpen = () => menu.classList.contains("is-open");

    toggle.addEventListener("click", () => {
        setMenu(!isOpen());
    });

    /* Any navigation link dismisses the panel, including same-page links. */
    menu.addEventListener("click", (event) => {
        if (event.target.closest("a")) {
            setMenu(false);
        }
    });

    document.addEventListener("keydown", (event) => {
        if (event.key === "Escape" && isOpen()) {
            setMenu(false);
            toggle.focus();
        }
    });

    /* Crossing back to desktop must never leave the body scroll-locked. */
    desktop.addEventListener("change", (event) => {
        if (event.matches) {
            setMenu(false);
        }
    });
});

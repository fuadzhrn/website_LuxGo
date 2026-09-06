document.addEventListener("DOMContentLoaded", () => {
    const sidebar = document.querySelector("[data-admin-sidebar]");
    const toggle = document.querySelector("[data-admin-toggle]");
    const scrim = document.querySelector("[data-admin-scrim]");

    if (!sidebar || !toggle || !scrim) {
        return;
    }

    const desktop = window.matchMedia("(min-width: 1024px)");

    const setOpen = (isOpen) => {
        sidebar.classList.toggle("is-open", isOpen);
        scrim.hidden = !isOpen;
        toggle.setAttribute("aria-expanded", String(isOpen));
        toggle.setAttribute("aria-label", isOpen ? "Close navigation" : "Open navigation");
    };

    const isOpen = () => sidebar.classList.contains("is-open");

    toggle.addEventListener("click", () => setOpen(!isOpen()));

    scrim.addEventListener("click", () => setOpen(false));

    /* Following a link should not leave the drawer covering the next page. */
    sidebar.addEventListener("click", (event) => {
        if (event.target.closest("a")) {
            setOpen(false);
        }
    });

    document.addEventListener("keydown", (event) => {
        if (event.key === "Escape" && isOpen()) {
            setOpen(false);
            toggle.focus();
        }
    });

    /* Crossing back to desktop must not leave a stale scrim over the page. */
    desktop.addEventListener("change", (event) => {
        if (event.matches) {
            setOpen(false);
        }
    });
});

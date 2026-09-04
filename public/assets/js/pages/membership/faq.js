document.addEventListener("DOMContentLoaded", () => {
    const faq = document.querySelector("[data-faq]");

    if (!faq) {
        return;
    }

    const questions = Array.from(faq.querySelectorAll("[data-faq-question]"));

    if (!questions.length) {
        return;
    }

    const setOpen = (button, isOpen) => {
        const answer = document.getElementById(button.getAttribute("aria-controls"));

        button.setAttribute("aria-expanded", String(isOpen));

        if (answer) {
            answer.hidden = !isOpen;
        }
    };

    /*
       The markup ships with every answer visible, so the content is readable
       when JavaScript never runs. Collapsing happens here instead.
    */
    questions.forEach((button, index) => setOpen(button, index === 0));

    questions.forEach((button) => {
        button.addEventListener("click", () => {
            const isOpen = button.getAttribute("aria-expanded") === "true";

            questions.forEach((other) => setOpen(other, false));
            setOpen(button, !isOpen);
        });
    });
});

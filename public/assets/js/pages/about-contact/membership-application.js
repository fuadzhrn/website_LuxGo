document.addEventListener("DOMContentLoaded", () => {
    const form = document.querySelector("[data-membership-application]");

    if (!form) {
        return;
    }

    const status = form.querySelector("[data-apply-status]");

    const errorFor = (field) => {
        const target = document.getElementById(`${field.id}-error`);

        return target && target.hasAttribute("data-apply-error") ? target : null;
    };

    const setError = (field, message) => {
        const target = errorFor(field);

        field.setAttribute("aria-invalid", "true");

        if (target) {
            target.textContent = message;
            target.hidden = false;
        }
    };

    const clearError = (field) => {
        const target = errorFor(field);

        field.removeAttribute("aria-invalid");

        if (target) {
            target.textContent = "";
            target.hidden = true;
        }
    };

    /*
       Deliberately loose: enough to catch a typo, not enough to reject an address
       a real mail server would accept.
    */
    const looksLikeEmail = (value) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);

    const validate = () => {
        const invalid = [];

        [
            ["apply-name", form.dataset.errorName],
            ["apply-phone", form.dataset.errorPhone],
        ].forEach(([id, message]) => {
            const field = form.querySelector(`#${id}`);

            if (!field) {
                return;
            }

            if (field.value.trim() === "") {
                setError(field, message);
                invalid.push(field);
            } else {
                clearError(field);
            }
        });

        const email = form.querySelector("#apply-email");

        if (email) {
            const value = email.value.trim();

            if (value === "") {
                setError(email, form.dataset.errorEmailRequired);
                invalid.push(email);
            } else if (!looksLikeEmail(value)) {
                setError(email, form.dataset.errorEmailInvalid);
                invalid.push(email);
            } else {
                clearError(email);
            }
        }

        const lots = form.querySelector("#apply-lots");

        if (lots) {
            const value = lots.value.trim();

            if (value !== "" && (!Number.isInteger(Number(value)) || Number(value) < 1)) {
                setError(lots, form.dataset.errorLots);
                invalid.push(lots);
            } else {
                clearError(lots);
            }
        }

        return invalid;
    };

    form.addEventListener("submit", (event) => {
        /*
           Submission stays blocked in both outcomes. There is no endpoint yet, so
           reporting anything as sent would be untrue — the backend stage wires this up.
        */
        event.preventDefault();

        const invalid = validate();

        if (invalid.length > 0) {
            if (status) {
                status.textContent = "";
            }

            invalid[0].focus();

            return;
        }

        if (status) {
            status.textContent = form.dataset.statusUnavailable;
        }
    });

    form.addEventListener("input", (event) => {
        const field = event.target;

        if (field.hasAttribute("aria-invalid")) {
            clearError(field);
        }
    });
});

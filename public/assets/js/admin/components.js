/*
   Behaviour for the reusable CMS components. Every control still works without
   this file: tabs fall back to visible panels, the image field is a plain file
   input, and alerts simply stay on screen.
*/
document.addEventListener("DOMContentLoaded", () => {
    initLanguageTabs();
    initImageFields();
    initStatusToggles();
    initAlerts();
    initSubmitGuards();
});

/* Language tabs ---------------------------------------------------------- */

function initLanguageTabs() {
    document.querySelectorAll("[data-admin-tabs]").forEach((group) => {
        const tabs = Array.from(group.querySelectorAll("[data-tab-target]"));
        const panels = Array.from(group.querySelectorAll("[data-tab-panel]"));

        if (tabs.length === 0) {
            return;
        }

        const activate = (locale, focus = false) => {
            tabs.forEach((tab) => {
                const isActive = tab.dataset.tabTarget === locale;
                tab.classList.toggle("is-active", isActive);
                tab.setAttribute("aria-selected", String(isActive));
                tab.tabIndex = isActive ? 0 : -1;

                if (isActive && focus) {
                    tab.focus();
                }
            });

            panels.forEach((panel) => {
                panel.hidden = panel.dataset.tabPanel !== locale;
            });
        };

        tabs.forEach((tab) => {
            tab.addEventListener("click", () => activate(tab.dataset.tabTarget));

            /* Arrow keys move between tabs, which is what a tablist is expected
               to do once the tabs are no longer plain links. */
            tab.addEventListener("keydown", (event) => {
                const index = tabs.indexOf(tab);
                let next = null;

                if (event.key === "ArrowRight") {
                    next = tabs[(index + 1) % tabs.length];
                } else if (event.key === "ArrowLeft") {
                    next = tabs[(index - 1 + tabs.length) % tabs.length];
                } else if (event.key === "Home") {
                    next = tabs[0];
                } else if (event.key === "End") {
                    next = tabs[tabs.length - 1];
                }

                if (next) {
                    event.preventDefault();
                    activate(next.dataset.tabTarget, true);
                }
            });
        });

        /* A field with an error should not be hidden behind an inactive tab. */
        const invalidPanel = panels.find((panel) => panel.querySelector("[aria-invalid='true']"));

        if (invalidPanel) {
            activate(invalidPanel.dataset.tabPanel);
        }
    });
}

/* Image field ------------------------------------------------------------ */

function initImageFields() {
    document.querySelectorAll("[data-admin-image]").forEach((field) => {
        const input = field.querySelector("[data-image-input]");
        const preview = field.querySelector("[data-image-preview]");
        const placeholder = field.querySelector("[data-image-placeholder]");
        const state = field.querySelector("[data-image-state]");
        const removeButton = field.querySelector("[data-image-remove]");
        const undoButton = field.querySelector("[data-image-undo]");
        const removeFlag = field.querySelector("[data-image-remove-flag]");

        if (!input || !preview || !removeFlag) {
            return;
        }

        /* Remembered so Undo can put the original frame back without a reload. */
        const originalSrc = preview.getAttribute("src") || "";
        const hadImage = !preview.hidden && originalSrc !== "";
        let objectUrl = null;

        const setState = (text, isRemoving) => {
            if (!state) {
                return;
            }

            state.textContent = text || "";
            state.hidden = !text;
            state.classList.toggle("admin-image__state--removing", Boolean(isRemoving));
        };

        input.addEventListener("change", () => {
            const file = input.files && input.files[0];

            if (!file) {
                return;
            }

            /* Choosing a file only draws a preview — nothing is uploaded until
               the form is saved. */
            if (objectUrl) {
                URL.revokeObjectURL(objectUrl);
            }

            objectUrl = URL.createObjectURL(file);
            preview.src = objectUrl;
            preview.hidden = false;

            if (placeholder) {
                placeholder.hidden = true;
            }

            removeFlag.value = "0";
            field.classList.remove("is-removing");

            if (removeButton) {
                removeButton.hidden = false;
            }

            if (undoButton) {
                undoButton.hidden = !hadImage;
            }

            setState(hadImage ? "New image selected. The current one is replaced on save." : "New image selected. It is uploaded on save.", false);
        });

        if (removeButton) {
            removeButton.addEventListener("click", () => {
                /* Marks intent only. The record and the file are dealt with by
                   the save handler, so the change can still be undone. */
                removeFlag.value = "1";
                input.value = "";
                preview.hidden = true;
                field.classList.add("is-removing");

                if (placeholder) {
                    placeholder.hidden = false;
                }

                removeButton.hidden = true;

                if (undoButton) {
                    undoButton.hidden = false;
                }

                setState("Image will be removed on save.", true);
            });
        }

        if (undoButton) {
            undoButton.addEventListener("click", () => {
                removeFlag.value = "0";
                input.value = "";
                field.classList.remove("is-removing");

                if (objectUrl) {
                    URL.revokeObjectURL(objectUrl);
                    objectUrl = null;
                }

                preview.src = originalSrc;
                preview.hidden = !hadImage;

                if (placeholder) {
                    placeholder.hidden = hadImage;
                }

                if (removeButton) {
                    removeButton.hidden = !hadImage;
                }

                undoButton.hidden = true;
                setState("", false);
            });
        }
    });
}

/* Status toggle ---------------------------------------------------------- */

function initStatusToggles() {
    document.querySelectorAll("[data-status-toggle]").forEach((input) => {
        const label = input.closest(".admin-toggle")?.querySelector("[data-status-label]");

        if (!label) {
            return;
        }

        input.addEventListener("change", () => {
            label.textContent = input.checked ? "Active" : "Inactive";
        });
    });
}

/* Alerts ----------------------------------------------------------------- */

function initAlerts() {
    document.querySelectorAll("[data-alert-dismiss]").forEach((button) => {
        button.addEventListener("click", () => {
            button.closest("[data-admin-alert]")?.remove();
        });
    });
}

/* Save action ------------------------------------------------------------ */

function initSubmitGuards() {
    document.querySelectorAll("form").forEach((form) => {
        const button = form.querySelector("[data-admin-submit]");

        if (!button) {
            return;
        }

        form.addEventListener("submit", () => {
            /* Disabled after the browser has collected the form, so a double
               click cannot post twice. */
            window.setTimeout(() => {
                button.disabled = true;
                button.dataset.busyLabel = button.textContent;
                button.textContent = "Saving…";
            }, 0);
        });
    });
}

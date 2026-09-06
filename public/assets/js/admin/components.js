/*
   Behaviour for the reusable CMS components. Every control still works without
   this file: tabs fall back to visible panels, the image field is a plain file
   input, and alerts simply stay on screen.
*/
document.addEventListener("DOMContentLoaded", () => {
    initLanguageTabs();
    initImageFields();
    initStatusToggles();
    initMediaPicker();
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
        const originalMediaId = field.querySelector("[data-image-media-id]")?.value || "";
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

        const chooseButton = field.querySelector("[data-image-choose]");
        const mediaIdInput = field.querySelector("[data-image-media-id]");

        if (chooseButton) {
            chooseButton.addEventListener("click", () => {
                window.dispatchEvent(new CustomEvent("admin:media-picker:open", { detail: { field } }));
            });
        }

        /* Applied when the picker reports a choice for this field. */
        field.addEventListener("admin:media-selected", (event) => {
            const { id, url, alt } = event.detail;

            if (mediaIdInput) {
                mediaIdInput.value = id;
            }

            /* An existing pick and a fresh upload are mutually exclusive. */
            input.value = "";

            if (objectUrl) {
                URL.revokeObjectURL(objectUrl);
                objectUrl = null;
            }

            preview.src = url;
            preview.alt = alt || "";
            preview.hidden = url === "";

            if (placeholder) {
                placeholder.hidden = url !== "";
            }

            removeFlag.value = "0";
            field.classList.remove("is-removing");

            if (removeButton) {
                removeButton.hidden = false;
            }

            if (undoButton) {
                undoButton.hidden = false;
            }

            setState("Existing media selected. Applied on save.", false);
        });

        if (removeButton) {
            removeButton.addEventListener("click", () => {
                /* Marks intent only. The record and the file are dealt with by
                   the save handler, so the change can still be undone. */
                removeFlag.value = "1";
                input.value = "";

                if (mediaIdInput) {
                    mediaIdInput.value = "";
                }
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

                if (mediaIdInput) {
                    mediaIdInput.value = originalMediaId;
                }
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

/* Media picker ----------------------------------------------------------- */

function initMediaPicker() {
    const picker = document.querySelector("[data-media-picker]");

    if (!picker) {
        return;
    }

    /* The field that opened the picker; the choice is handed back to it alone. */
    let requester = null;
    let lastFocused = null;

    const close = () => {
        picker.hidden = true;
        document.body.classList.remove("has-picker-open");
        requester = null;
        lastFocused?.focus();
    };

    window.addEventListener("admin:media-picker:open", (event) => {
        requester = event.detail.field;
        lastFocused = document.activeElement;
        picker.hidden = false;
        document.body.classList.add("has-picker-open");
        picker.querySelector("[data-picker-select], [data-picker-close]")?.focus();
    });

    picker.querySelectorAll("[data-picker-close]").forEach((button) => {
        button.addEventListener("click", close);
    });

    picker.querySelectorAll("[data-picker-select]").forEach((button) => {
        button.addEventListener("click", () => {
            if (!requester) {
                return;
            }

            /* Only a reference travels back — no file is copied or moved. */
            requester.dispatchEvent(new CustomEvent("admin:media-selected", {
                detail: {
                    id: button.dataset.mediaId,
                    url: button.dataset.mediaUrl || "",
                    alt: button.dataset.mediaAlt || "",
                },
            }));

            close();
        });
    });

    document.addEventListener("keydown", (event) => {
        if (event.key === "Escape" && !picker.hidden) {
            close();
        }
    });
}

document.addEventListener("DOMContentLoaded", () => {
    const calculator = document.querySelector("[data-calculator]");

    if (!calculator) {
        return;
    }

    const decreaseButton = calculator.querySelector("[data-calculator-decrease]");
    const increaseButton = calculator.querySelector("[data-calculator-increase]");
    const lotsOutput = calculator.querySelector("[data-calculator-lots]");
    const annualOutput = calculator.querySelector("[data-calculator-annual]");
    const totalOutput = calculator.querySelector("[data-calculator-total]");

    if (!decreaseButton || !increaseButton || !lotsOutput || !annualOutput || !totalOutput) {
        return;
    }

    const MIN_LOTS = 1;
    /* Safeguard for the input only — not a business limit on LOTs. */
    const MAX_LOTS = 99;

    /* The rule comes from the page, which renders it from the membership
       settings. No business figure is written here, so changing a setting is
       enough — this file never needs editing. */
    const number = (name) => Number.parseInt(calculator.dataset[name], 10);

    const BASE_RIGHTS = number("baseRights");
    const RIGHTS_PER_ADDITIONAL_LOT = number("additionalRights");
    const MEMBERSHIP_YEARS = number("period");

    if (!Number.isFinite(BASE_RIGHTS) || !Number.isFinite(RIGHTS_PER_ADDITIONAL_LOT) || !Number.isFinite(MEMBERSHIP_YEARS)) {
        /* Without the figures the server-rendered values stay as they are,
           rather than being replaced with something invented here. */
        return;
    }

    let lots = MIN_LOTS;

    const render = () => {
        const annualRights = BASE_RIGHTS + (lots - MIN_LOTS) * RIGHTS_PER_ADDITIONAL_LOT;
        const totalRights = annualRights * MEMBERSHIP_YEARS;

        lotsOutput.textContent = String(lots);
        annualOutput.textContent = annualRights + "\u00d7";
        totalOutput.textContent = totalRights + "\u00d7";

        decreaseButton.disabled = lots <= MIN_LOTS;
        increaseButton.disabled = lots >= MAX_LOTS;
    };

    const setLots = (value) => {
        lots = Math.min(MAX_LOTS, Math.max(MIN_LOTS, value));
        render();
    };

    decreaseButton.addEventListener("click", () => setLots(lots - 1));
    increaseButton.addEventListener("click", () => setLots(lots + 1));

    render();
});

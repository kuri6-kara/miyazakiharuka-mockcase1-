document.addEventListener("DOMContentLoaded", function () {
    const selectElement = document.getElementById("payment_method_select");
    const displaySpan = document.getElementById("selected-payment-name");
    const changeLink = document.getElementById("change-address-link");

    if (selectElement && displaySpan) {
        if (selectElement.selectedIndex !== -1 && selectElement.value !== "") {
            displaySpan.textContent =
                selectElement.options[selectElement.selectedIndex].text;
        } else {
            displaySpan.textContent = "---";
        }
    }

    if (selectElement && displaySpan) {
        selectElement.addEventListener("change", function () {
            const selectedText = this.options[this.selectedIndex].text;
            displaySpan.textContent = selectedText;
            if (changeLink) {
                updateChangeLink();
            }
        });
    }

    if (changeLink && selectElement) {
        const baseUrl = changeLink.getAttribute("data-base-url");

        function updateChangeLink() {
            if (!baseUrl) {
                console.error(
                    "Base URL for change-address-link is missing in data-base-url attribute."
                );
                return;
            }
            const selectedId = selectElement.value;
            changeLink.href = baseUrl + "?payment_method_id=" + selectedId;
        }

        updateChangeLink();
    }
});

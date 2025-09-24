document.addEventListener("DOMContentLoaded", () => {
    const tabs = document.querySelectorAll(".tab-item");
    const contents = document.querySelectorAll(".items-list");

    tabs.forEach((tab) => {
        tab.addEventListener("click", () => {
            tabs.forEach((t) => t.classList.remove("active"));
            contents.forEach((c) => c.classList.remove("active"));

            tab.classList.add("active");

            const targetId = tab.dataset.tabTarget;
            const targetContent = document.getElementById(targetId);
            if (targetContent) {
                targetContent.classList.add("active");
            }
        });
    });
});

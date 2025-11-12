document.addEventListener("DOMContentLoaded", () => {
    const imageInput = document.getElementById("item_image");
    const preview = document.getElementById("image-preview");
    const placeholder = document.getElementById("image-placeholder");

    if (imageInput) {
        imageInput.addEventListener("change", function (event) {
            const file = event.target.files[0];

            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    preview.src = e.target.result;
                    preview.style.display = "block";
                    placeholder.style.display = "none";
                };
                reader.readAsDataURL(file);
            } else {
                preview.src = "#";
                preview.style.display = "none";
                placeholder.style.display = "block";
            }
        });
    }

    const categoryLabels = document.querySelectorAll(
        ".category-tags label.tag"
    );

    categoryLabels.forEach((label) => {
        const checkboxId = label.getAttribute("for");
        const checkbox = document.getElementById(checkboxId);

        if (checkbox) {
            if (checkbox.checked) {
                label.classList.add("tag-active");
            }

            label.addEventListener("click", function (e) {
                if (checkbox.checked) {
                    this.classList.remove("tag-active");
                }
                else {
                    this.classList.add("tag-active");
                }
            });
        }
    });
});

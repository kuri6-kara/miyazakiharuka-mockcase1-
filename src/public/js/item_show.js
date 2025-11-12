document.addEventListener("DOMContentLoaded", function () {
    const likeButton = document.getElementById("like-button");
    const likeIcon = document.getElementById("like-icon");
    const likesCountSpan = document.getElementById("likes-count");

    function updateLikeIcon(isLiked) {
        const imgElement = likeIcon.querySelector("img");
        if (imgElement) {
            imgElement.alt = isLiked ? "いいね済みアイコン" : "いいねアイコン";
        }
    }

    if (likeButton) {
        const csrfToken = document
            .querySelector('meta[name="csrf-token"]')
            .getAttribute("content");

        likeButton.addEventListener("click", function () {
            const itemId = this.dataset.itemId;
            let isLiked = this.dataset.isLiked === "true";
            let currentCount = parseInt(likesCountSpan.textContent);

            fetch(`/items/${itemId}/like`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken,
                },
                body: JSON.stringify({
                    item_id: itemId,
                }),
            })
                .then((response) => response.json())
                .then((data) => {
                    if (data.status === "success") {
                        isLiked = data.action === "attached";
                        this.dataset.isLiked = isLiked ? "true" : "false";

                        if (isLiked) {
                            this.classList.add("liked");
                            likesCountSpan.textContent = currentCount + 1;
                        } else {
                            this.classList.remove("liked");
                            likesCountSpan.textContent = currentCount - 1;
                        }
                        updateLikeIcon(isLiked);
                    } else if (
                        data.status === "error" &&
                        data.message === "unauthenticated"
                    ) {
                        window.location.href = "/login";
                    }
                })
                .catch((error) => {
                    console.error("Error:", error);
                });
        });
    }
});

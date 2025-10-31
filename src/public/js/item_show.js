// 商品詳細画面 (items.show) のためのJavaScript

document.addEventListener("DOMContentLoaded", function () {
    const likeButton = document.getElementById("like-button");
    const likeIcon = document.getElementById("like-icon");
    const likesCountSpan = document.getElementById("likes-count");

    // アイコン画像要素を操作するための関数
    function updateLikeIcon(isLiked) {
        const imgElement = likeIcon.querySelector("img");
        if (imgElement) {
            // 画像の色を変えるロジックや、別々の画像ファイルを切り替えるロジックを実装する場合に利用します。
            // 現在のBladeテンプレートでは同じ画像 ('icon/star-icon.png') を使ってCSSで制御しているため、
            // ここではクラスのトグルを通じてCSSに反映させます。
            imgElement.alt = isLiked ? "いいね済みアイコン" : "いいねアイコン";
        }
    }

    if (likeButton) {
        // CSRFトークンを取得
        // Bladeで<meta name="csrf-token" content="{{ csrf_token() }}">が定義されていることを前提とします。
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
                        // 状態を反転
                        isLiked = data.action === "attached";
                        this.dataset.isLiked = isLiked ? "true" : "false";

                        // ビューを更新
                        if (isLiked) {
                            this.classList.add("liked");
                            likesCountSpan.textContent = currentCount + 1;
                        } else {
                            this.classList.remove("liked");
                            likesCountSpan.textContent = currentCount - 1;
                        }
                        // アイコン画像を更新 (主にaltテキストを更新)
                        updateLikeIcon(isLiked);
                    } else if (
                        data.status === "error" &&
                        data.message === "unauthenticated"
                    ) {
                        // 認証エラーの場合
                        window.location.href = "/login";
                    }
                })
                .catch((error) => {
                    console.error("Error:", error);
                });
        });
    }
});

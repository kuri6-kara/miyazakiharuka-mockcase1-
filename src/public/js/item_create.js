// 商品出品画面 (items.create) のためのJavaScript

document.addEventListener("DOMContentLoaded", () => {
    // --- 1. 画像プレビュー機能 ---
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

    // --- 2. カテゴリタグのスタイル切り替え ---
    const categoryLabels = document.querySelectorAll(
        ".category-tags label.tag"
    );

    categoryLabels.forEach((label) => {
        const checkboxId = label.getAttribute("for");
        const checkbox = document.getElementById(checkboxId);

        if (checkbox) {
            // A. ページ読み込み時の初期状態設定 (old()の値に基づく)
            if (checkbox.checked) {
                label.classList.add("tag-active");
            }

            // B. クリック時のスタイル切り替え
            label.addEventListener("click", function (e) {
                // チェックボックスの状態がトグルされることを前提に、その後の状態を反映
                // クリックイベントハンドラが実行される時点では、チェックボックスの状態はまだ変わる前であるため、
                // `checkbox.checked` の現在の状態を見て、次にトグルされる状態（つまり、反対の状態）に基づいてクラスを制御します。

                // checkbox.checked == true (現在チェック済み) -> 次はチェック解除される -> tag-activeを削除
                if (checkbox.checked) {
                    this.classList.remove("tag-active");
                }
                // checkbox.checked == false (現在未チェック) -> 次はチェックされる -> tag-activeを追加
                else {
                    this.classList.add("tag-active");
                }
            });
        }
    });
});

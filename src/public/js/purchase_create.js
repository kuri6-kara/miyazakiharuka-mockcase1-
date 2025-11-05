// 商品購入画面 (purchases.create) のためのJavaScript

document.addEventListener("DOMContentLoaded", function () {
    const selectElement = document.getElementById("payment_method_select");
    const displaySpan = document.getElementById("selected-payment-name");
    const changeLink = document.getElementById("change-address-link"); // 新しく追加

    // 1. 初期表示時の設定
    // ページロード時、選択されているオプションのテキストをサマリーに表示
    if (selectElement && displaySpan) {
        if (selectElement.selectedIndex !== -1 && selectElement.value !== "") {
            // valueが空でない（つまり「選択してください」ではない）オプションのテキストを表示
            displaySpan.textContent =
                selectElement.options[selectElement.selectedIndex].text;
        } else {
            // オプションが選択されていない、または「選択してください」の場合
            displaySpan.textContent = "---";
        }
    }

    // 2. 選択肢変更時のイベントリスナー設定 (サマリー更新)
    if (selectElement && displaySpan) {
        selectElement.addEventListener("change", function () {
            // 選択されたオプションのテキストを取得
            const selectedText = this.options[this.selectedIndex].text;
            // サマリーの表示を更新
            displaySpan.textContent = selectedText;

            // 配送先変更リンクのURLも同時に更新
            if (changeLink) {
                updateChangeLink();
            }
        });
    }

    // 3. 配送先変更リンクの動的更新機能 (新規追加)
    // この機能はBladeファイルからJS変数経由でベースURL（商品ID含む）を受け取る必要があります。
    // 例: <a href="#" id="change-address-link" data-base-url="{{ route('purchase.edit', ['item_id' => $item->id]) }}">変更する</a>
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
            // payment_method_idをクエリパラメータとして追加
            changeLink.href = baseUrl + "?payment_method_id=" + selectedId;
        }

        // 初期ロード時にもリンクを更新
        updateChangeLink();
    }
});

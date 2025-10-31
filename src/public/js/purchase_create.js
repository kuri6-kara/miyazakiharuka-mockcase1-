// 商品購入画面 (purchases.create) のためのJavaScript

document.addEventListener("DOMContentLoaded", function () {
    const select = document.getElementById("payment_method_select");
    const displaySpan = document.getElementById("selected-payment-name");

    // 1. 初期表示時の設定
    // ページロード時、選択されているオプションのテキストをサマリーに表示
    if (select && displaySpan) {
        // nullチェック
        if (select.selectedIndex !== -1) {
            displaySpan.textContent = select.options[select.selectedIndex].text;
        } else {
            // オプションが選択されていない場合のフォールバック（例: "選択してください"）
            displaySpan.textContent = "---";
        }
    }

    // 2. 選択肢変更時のイベントリスナー設定
    if (select && displaySpan) {
        select.addEventListener("change", function () {
            // 選択されたオプションのテキストを取得
            const selectedText = this.options[this.selectedIndex].text;
            // サマリーの表示を更新
            displaySpan.textContent = selectedText;
        });
    }
});

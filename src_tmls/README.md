# [PLUGIN_NAME]


## Installation

## Custom post types
*

## Meta

### Post meta
*

### User meta
*

## Hooks
*

## Shortcodes
*

## Todo
*

## Dev Memo

### 後台設定頁

無論要用「制式設定區」還是「完全客製」，都是由 admin/settings/ 底下的 class-plugin-slug-settings.php 開始設定。class-plugin-slug-settings.php 透過 add_menu_items() 建立選單項目和對應的設定頁面。

#### 定義設定頁面的內容
直接於 add_menu_items() 建立新的設定，對應到輸出頁面內容的 Functions，在該 Function 裡進行實作即可。

設定頁面裡的 Layout 方式若是要使用「制式設定區」，就會呈現頁籤(Tab)式，並且大多是一行一組元件的制式風格，適合較單純和制式的設定 Layout。

制式設定區的頁面設定可參考: display_settings_page()，它會輸出「制式設定區」的設定檔內容，但要先完成定義宣告。

#### 「制式設定區」的定義宣告：

它 include class-plugin-slug-settings-abstract.php 就是定義制式設定區的各種 Functions：
* get_fd_option: 取出設定頁的某個元件欄位的值
* get_fd_setting: 取出設定頁的某個元件設定的值，用於畫出元件
* 定義各種元件的呈現方法:
    1. hidden
    2. upload: 上傳檔案成為 wp attachment
    3. buttn
    4. date: datepicker
    5. input text
    6. input checkbox
    7. select

接著 include class-plugin-slug-settings-factory 工廠定義可生成的「設定區物件」有哪些，要在外部取得「制式設定區」的設定值，也是透過工廠取得特定設定區物件。

接著 include 其他設定區，如 general 設定區 (class-plugin-slug-settings-general.php) 定義設定區會出現的元件，也能指定要將設定欄位值儲存在哪個 option key (例如: 要覆寫 WP 後台預設的某個 option 設定值)

準備好客製設定區的定義檔案後，class-plugin-slug-settings.php 接著會:
* 透過 register() 註冊制式設定區：
    * 將建構子定義的設定區註冊好，建立 Tab 式的設定區及其元件。
    * 透過 register_settings() 指定要儲存的 option，利用 Sanitize Function (儲存前消毒)，指定執行 handle_before_save()
    * 一般元件都是直接將值儲存至指定的 option，若是 upload 則要另行客製處理建立 attachment，所以定義了 upload_and_save_as_attachemnt()，在 handle_before_save() 裡執行
* 其他設定還有 add_log() 和 clear_log() 是用來記錄和清除 log

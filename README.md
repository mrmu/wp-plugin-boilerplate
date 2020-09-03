# WordPress Plugin Boilerplate Generator

<img src="https://user-images.githubusercontent.com/271049/65505625-37c08700-defc-11e9-98a1-39f1b158ab34.png">

### Usage
After install and activate wppb plugin, you can use shortcode [wppb_form] to show the generator form like wppb.me.

### Customization

1. How to add my own code to the boilerplates?
All the plugin boilerplate files stored in src_tmls/, you can modify them but please keep the pattern words for replacement.

1. How to customize wppb form?
Please crate a php file named "wppb-form.php" and place it under your current theme directory and then refer to templates/wppb-form.php to customize it.

### 使用方法
安裝本外掛後，直接使用 [wppb_form] 來顯示如同 wppb.me 的提交表單。

### 客製方式

1. 怎麼加上自己的程式碼到生成的模版內？
所有的模版檔案都儲存在 src_tmls/ 下，注意不要破壞取代樣版即可。

1. 如何客製表單？
請在你目前使用的佈景目錄下建立 wppb-form.php 檔案，再參考外掛目錄下的 templates/wppb-form.php 進行客製修改。

### Features
* 2020/09/03
    * 重新命名 src_tmls/ 
    * 整理 settings，增加欄位類型
* 2020/07/13
    * google recaptcha v2 (use shortcode to test)
    * wc_mail()
* 2020/6/27
    * class 相依性檢查設定及後台通知: $class_deps_check (如：使用本外掛前，必須先安裝 woocommerce)
    * 後台 css, js 僅在特定頁載入的設定: is_enqueue_pages()
    * ajax functions sample code
    * 後台自訂選單、設定頁: settings classes in Singleton, Factory
    * 後台自帶 Logger

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
* 後台設定頁：
admin/settings/ 底下的 xxx_settings.php 會 include :
    1. xxx_abstract.php: 各種元件載入資料及呈現的 DOM 形式
    2. xxx_factory.php: 回傳每個設定頁對應的類別實體
    3. 其他的設定頁.php
如果要增加支援的設定元件(DOM)，就要到 abstract.php 加入；若是要增加設定頁，就要 :
    1. 參考 xxx_general.php 新增設定頁.php
    2. 於 xxx_settings.php include 新的設定頁.php
    2. 到 xxx_factory.php 增加對應的新類別實體

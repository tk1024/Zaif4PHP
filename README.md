Zaif4PHP
==========

暗号通貨取引所Zaif用のライブラリです。

##準備

```php
require 'zaif.php';
```

##使い方
関数(エンドポイント,オプション)というTwitterライブラリによくある仕様にしました。  
  
  
PublicAPIを使う場合を含めまずはインスタントを作ります。
```php
$key = "YOUR API KEY";
$secret = "YOUR SECRET KEY";

$zaif = new Zaif($key, $secret);
```
###Public API(例)
```php
$data = $zaif->pub("last_price","btc_jpy");
```

###Trade API(例)
```php
$data = $zaif->trade("get_info");
```
###Trade API(例2)
```php
$data = $zaif->trade("trade", array('currency_pair' => 'mona_jpy', 'action' => 'ask', 'price' => 10000, 'amount' => 1 ) );
```

##注意
 - 例外処理が不十分かもしれません。
 - json_decodeの必要はありません。
 - cURLが必須です。
 
##公式ドキュメント
APIの詳細などは以下をご覧下さい。  
https://zaif.jp/doc_api

##その他
Mona: MLreYfG2CyefY8MdnasAUnGpG44RV2d6PV

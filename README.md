Zaif4PHP
==========

暗号通貨取引所Zaif用のライブラリです。  
一応、ストリーミングも対応しています。  

##準備

```php
require 'Zaif.php';
```

##使い方
function(endpoint,options)というTwitterライブラリによくある仕様にしました。  
  
  
TradeAPIを使う場合はインスタンスを作ります。
```php
$key = "YOUR API KEY";
$secret = "YOUR API SECRET";

$zaif = new Zaif($key, $secret);
```
###Public API(例)
```php
$data = Zaif::pub("last_price","btc_jpy");
```

###Trade API(例)
```php
$data = $zaif->trade("get_info");
```
###Trade API(例2)
```php
$data = $zaif->trade("trade", array('currency_pair' => 'mona_jpy', 'action' => 'ask', 'price' => 10000, 'amount' => 1 ) );
```
##Streamimg API
一応、ストリーミングAPIにも対応しています。(Linux系のみ)  
この機能を使うには下準備が必要です。  

Zaif.php が置いてあるディレクトリで
```
php -r "readfile('https://getcomposer.org/installer');" | php
php composer.phar install
```
このコマンドを実行し、composer 及び Websocket Client for PHP を導入します。  
これで準備完了です。

##Streaming API 使い方
```php
$zaif->streaming(array('currency_pair'=>'mona_jpy'), function($data){
	var_dump($data);
});
```
このような感じでStreaming APIを扱えます。

##注意
 - 例外処理が不十分かもしれません。
 - json_decodeの必要はありません。
 - cURLが必須です。
 - バグだらけだと思うので、重要な事に使うならご自身で一度、コードを精査する事をお勧めします。
 
##公式ドキュメント
APIの詳細などは以下をご覧下さい。  
https://zaif.jp/doc_api

##その他
Mona: MLreYfG2CyefY8MdnasAUnGpG44RV2d6PV

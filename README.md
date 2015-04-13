#Zaif4PHP

暗号通貨取引所ZaifのPHP用ライブラリです。
追加作業を行えばストリーミングAPIも使えます。

一部機能を除けば、Windowsでも動くと思いますが、Linuxでの動作を想定しています。

##事前に必要な物
・cURL

##導入
Public API, Trade APIだけを使う場合は'Zaif.php'を含めるだけで動きます。
Streaming API を使う場合はZaif.php が置いてあるディレクトリで
```
php -r "readfile('https://getcomposer.org/installer');" | php
php composer.phar install
```
このコマンドを実行して、composer 及び Websocket Client for PHP を導入して下さい。

##使い方

###はじめに

Public API, Trade API, Streaming APIどの場合でも最初に
```php
require 'Zaif.php';
```
この1行を書き、Zaif.phpをコードに含めるようにして下さい。

###返り値
JSONがデコードされた状態で値が帰ってきます。

###Public API

Public APIを使うのにAPI Keyを発行する必要はありません。
```php
//BTC_JPYの価格を取得する
$price = Zaif::pub("last_price","btc_jpy");

//MONA_JPYの板情報を取得する
$depth = Zaif::pub("depth","mona_jpy");

//出力
var_dump($price, $depth);
```
###Trade API

Trade APIを使うのにAPI Keyを発行する必要があります。
https://zaif.jp/api_keys で事前にAPI Keyを発行し、permsのtrade(情報を見る場合はinfoも)を有効にしておいて下さい。
```php
$key = "YOUR API KEY";
$secret = "YOUR API SECRET";

//インスタンスの生成
$zaif = new Zaif($key, $secret);

//残高,APIの権限,トレード数,アクティブな注文数,サーバーのタイムスタンプを取得する
$info = $zaif->trade("get_info");

//1モナ100円で15モナ売り板に出す
$trade_ask = $zaif->trade("trade",
	array(
		'currency_pair' => 'mona_jpy',
		'action' => 'ask',
		'price' => 100,
		'amount' => 15 
	)
);

//MONA_JPYの現在有効な注文一覧を表示する
$active_orders = $zaif->trade("active_orders", array('currency_pair' => 'mona_jpy'));

//出力
var_dump($info, $trade_ask, $active_orders);
```
###Streaming API

Streaming APIを使うのにAPI Keyを発行する必要はありません。
```php
Zaif::streaming(array('currency_pair' => 'mona_jpy'),function($data){
	//板の更新や取引が行われる毎に情報を表示
	var_dump($data);
});
```
##公式ドキュメント
APIの詳細などは以下をご覧下さい。  
https://zaif.jp/doc_api

##その他
Mona: MLreYfG2CyefY8MdnasAUnGpG44RV2d6PV

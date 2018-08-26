<?php

// assertを有効にし、出力を抑制する
assert_options(ASSERT_ACTIVE, 1);
assert_options(ASSERT_WARNING, 0);
assert_options(ASSERT_QUIET_EVAL, 1);

// ハンドラ関数を作成する
function my_assert_handler($file, $line, $code)
{
    var_dump($file, $line, $code);
    echo "<hr>Assertion Failed:
        File '$file'<br />
        Line '$line'<br />
        Code '$code'<br /><hr />";
}

// コールバックを設定する
assert_options(ASSERT_CALLBACK, 'my_assert_handler');

require(__DIR__ . "/../Zaif.php");

$key = getenv("KEY");
$secret = getenv("SECRET");
$zaif = new Zaif($key, $secret);

// get_info

$get_info = $zaif->trade(TradeApiEndpoint::GET_INFO);
assert($get_info->success === 1);

// get_info2

$get_info2 = $zaif->trade(TradeApiEndpoint::GET_INFO2);
assert($get_info2->success === 1);

// get_personal_info

$get_personal_info = $zaif->trade(TradeApiEndpoint::GET_PERSONAL_INFO);
assert($get_personal_info->success === 1);

// get_id_info

$get_id_info = $zaif->trade(TradeApiEndpoint::GET_ID_INFO);
assert($get_personal_info->success === 1);

// trade_history

$trade_history = $zaif->trade(TradeApiEndpoint::TRADE_HISTORY);
assert($trade_history->success === 1);

// trade_history

$active_orders = $zaif->trade(TradeApiEndpoint::ACTIVE_ORDERS);
assert($active_orders->success === 1);


// trade
/*
    注文が確定しない額で注文を入れ、その後キャンセルする
 */

$last_price = Zaif::pub(PublicApiEndpoint::LAST_PRICE, "btc_jpy");
$trade_btc_jpy_price = ceil($last_price->last_price * 0.6 / 10) * 10;
$trade = $zaif->trade(TradeApiEndpoint::TRADE, [
    "currency_pair" => "btc_jpy",
    "action" => "bid",
    "price" => $trade_btc_jpy_price,
    "amount" => 0.001
]);
assert($trade->success === 1);

sleep(5);

// cancel_order

$cancel_order = $zaif->trade(TradeApiEndpoint::CANCEL_ORDER, [
    "order_id" => $trade->return->order_id
]);
assert($cancel_order->success === 1);

// withdraw テスト未サポート

// deposit_history

$deposit_history = $zaif->trade(TradeApiEndpoint::DEPOSIT_HISTORY, [
    "currency" => "jpy"
]);

assert($deposit_history->success === 1);

// withdraw_history

$deposit_history = $zaif->trade(TradeApiEndpoint::WITHDRAW_HISTORY, [
    "currency" => "jpy"
]);

assert($deposit_history->success === 1);
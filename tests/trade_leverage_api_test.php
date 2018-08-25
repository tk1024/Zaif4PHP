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

// get_positions

$get_positions = $zaif->tradeLeverage(TradeLeverageApiEndpoint::GET_POSITIONS, [
    "type" => "margin",
    "count" => 1
]);
assert($get_positions->success === 1);

// get_positions

$position_history = $zaif->tradeLeverage(TradeLeverageApiEndpoint::POSTION_HISTORY, [
    "type" => "margin",
    "leverage_id" => 1982406
]);
assert($position_history->success === 1);

// get_positions

$active_positions = $zaif->tradeLeverage(TradeLeverageApiEndpoint::ACTIVE_POSITIONS, [
    "type" => "margin"
]);
assert($active_positions->success === 1);

// 取引のテストをする場合は自分で価格を設定し、実行してください
if (false) {
    $price = 720000;

    // create_position

    $create_position = $zaif->tradeLeverage(TradeLeverageApiEndpoint::CREATE_POSITION, [
        "type" => "margin",
        "currency_pair" => "btc_jpy",
        "action" => "bid",
        "price" => $price,
        "amount" => 0.001,
        "leverage" => 2.5
    ]);
    assert($create_position->success === 1);

    sleep(5);

    // change_position

    $change_position = $zaif->tradeLeverage(TradeLeverageApiEndpoint::CHANGE_POSITION, [
        "type" => "margin",
        "leverage_id" => $create_position->return->leverage_id,
        "price" => $price + 5
    ]);

    assert($change_position->success === 1);

    sleep(5);

    // cancel_position

    $change_position = $zaif->tradeLeverage(TradeLeverageApiEndpoint::CANCEL_POSITION, [
        "type" => "margin",
        "leverage_id" => $create_position->return->leverage_id
    ]);

    assert($change_position->success === 1);
}
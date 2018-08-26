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

// groups

$groups = Zaif::publicFutures(PublicFuturesApiEndpoint::GROUPS, [
    "all"
]);
assert($groups[0]->id === 1);

// last_price

$last_price = Zaif::publicFutures(PublicFuturesApiEndpoint::LAST_PRICE, [
    1,
    "btc_jpy"
]);
assert(gettype($last_price->last_price) === "double");

// ticker

$ticker = Zaif::publicFutures(PublicFuturesApiEndpoint::TICKER, [
    1,
    "btc_jpy"
]);
assert(gettype($ticker->last) === "double");

// trades

$trades = Zaif::publicFutures(PublicFuturesApiEndpoint::TRADES, [
    1,
    "btc_jpy"
]);
assert(gettype($trades[0]->price) === "double");

// depth

$depth = Zaif::publicFutures(PublicFuturesApiEndpoint::DEPTH, [
    1,
    "btc_jpy"
]);
assert(gettype($depth->asks[0][0]) === "double");

// swap_history

$swap_history = Zaif::publicFutures(PublicFuturesApiEndpoint::GROUPS, [
    1,
    "btc_jpy"
]);
assert($swap_history[0]->id === 1);
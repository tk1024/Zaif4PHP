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

require(__DIR__."/../Zaif.php");

// currencies

$currencies_btc = Zaif::pub(PublicApiEndpoint::CURRENCIES, "btc");
assert($currencies_btc[0]->name === "btc");

$currencies_all = Zaif::pub(PublicApiEndpoint::CURRENCIES, "all");
assert(gettype($currencies_all[0]->name) === "string");

// currency_pairs

$currency_pairs_btc_jpy = Zaif::pub(PublicApiEndpoint::CURRENCY_PAIRS, "btc_jpy");
assert($currency_pairs_btc_jpy[0]->title === "BTC/JPY");

$currency_pairs_btc_all = Zaif::pub(PublicApiEndpoint::CURRENCY_PAIRS, "all");
assert(gettype($currency_pairs_btc_all[0]->title) === "string");

// last_price

$last_price_btc_jpy = Zaif::pub(PublicApiEndpoint::LAST_PRICE, "btc_jpy");
assert(gettype($last_price_btc_jpy->last_price) === "double");

// ticker

$ticker_btc_jpy = Zaif::pub(PublicApiEndpoint::TICKER, "btc_jpy");
assert(gettype($ticker_btc_jpy->last) === "double");

// trades

$trades_btc_jpy = Zaif::pub(PublicApiEndpoint::TRADES, "btc_jpy");
assert(gettype($trades_btc_jpy[0]->price) === "double");

// depth

$depth_btc_jpy = Zaif::pub(PublicApiEndpoint::DEPTH, "btc_jpy");
assert(gettype($depth_btc_jpy->asks[0][0]) === "double");
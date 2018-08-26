<?php

use WebSocket\Client;

class PublicApiEndpoint
{
  const LAST_PRICE = "last_price";
  const TICKER = "ticker";
  const TRADES = "trades";
  const DEPTH = "depth";
  const CURRENCIES = "currencies";
  const CURRENCY_PAIRS = "currency_pairs";

  public static function getConstants()
  {
    $oClass = new ReflectionClass(__class__);
    return $oClass->getConstants();
  }
}

class TradeApiEndpoint
{
  const GET_INFO = "get_info";
  const GET_INFO2 = "get_info2";
  const GET_PERSONAL_INFO = "get_personal_info";
  const GET_ID_INFO = "get_id_info";
  const TRADE_HISTORY = "trade_history";
  const ACTIVE_ORDERS = "active_orders";
  const TRADE = "trade";
  const CANCEL_ORDER = "cancel_order";
  const WITHDRAW = "withdraw";
  const DEPOSIT_HISTORY = "deposit_history";
  const WITHDRAW_HISTORY = "withdraw_history";

  public static function getConstants()
  {
    $oClass = new ReflectionClass(__class__);
    return $oClass->getConstants();
  }
}

class PublicFuturesApiEndpoint
{
  const GROUPS = "groups";
  const LAST_PRICE = "last_price";
  const TICKER = "ticker";
  const TRADES = "trades";
  const DEPTH = "depth";
  const SWAP_HISTORY = "swap_history";

  public static function getConstants()
  {
    $oClass = new ReflectionClass(__class__);
    return $oClass->getConstants();
  }
}

class TradeLeverageApiEndpoint
{
  const GET_POSITIONS = "get_positions";
  const POSTION_HISTORY = "position_history";
  const ACTIVE_POSITIONS = "active_positions";
  const CREATE_POSITION = "create_position";
  const CHANGE_POSITION = "change_position";
  const CANCEL_POSITION = "cancel_position";

  public static function getConstants()
  {
    $oClass = new ReflectionClass(__class__);
    return $oClass->getConstants();
  }
}

class Zaif
{
  const PUBLIC_BASE_URL = "https://api.zaif.jp/api/1";
  const TRADE_BASE_URL = "https://api.zaif.jp/tapi";
  const PUBLIC_FUTURES_BASE_URL = "https://api.zaif.jp/fapi/1";
  const TRADE_LEVERAGE_BASE_URL = "https://api.zaif.jp/tlapi";
  const STREAMING_BASE_URL = "ws://api.zaif.jp:8888/stream";

  private $key;
  private $secret;
  private $nonce;

  public function __construct($key, $secret)
  {
    $this->key = $key;
    $this->secret = $secret;
    $this->nonce = time();
  }

  public static function pub($endpoint, $prm = "")
  {
    if (!in_array($endpoint, array_values(PublicApiEndpoint::getConstants()))) {
      throw new Exception("Argument has not been set.");
    }

    $url = self::PUBLIC_BASE_URL . "/" . $endpoint . "/" . $prm;
    $data = CurlWrapper::get($url);
    $data = json_decode($data);

    return $data;
  }

  public function trade($endpoint, $prms = null)
  {
    if (!in_array($endpoint, array_values(TradeApiEndpoint::getConstants()))) {
      throw new Exception("Argument has not been set.");
    }

    $postdata = [
      "nonce" => $this->getNonceWithIncrement(),
      "method" => $endpoint
    ];

    if (!empty($prms)) {
      $postdata = array_merge($postdata, $prms);
    }

    $postdata_query = http_build_query($postdata);
    $data = CurlWrapper::post(self::TRADE_BASE_URL, $this->getTradeHeader($postdata_query), $postdata_query);
    $data = json_decode($data);

    return $data;
  }

  public static function publicFutures($endpoint, $prms = []) {
    if (!in_array($endpoint, array_values(PublicFuturesApiEndpoint::getConstants()))) {
      throw new Exception("Argument has not been set.");
    }

    $url = self::PUBLIC_FUTURES_BASE_URL . "/" . $endpoint . "/" . implode("/", $prms);
    $data = CurlWrapper::get($url);
    $data = json_decode($data);

    return $data;

  }

  public function tradeLeverage($endpoint, $prms = null)
  {
    if (!in_array($endpoint, array_values(TradeLeverageApiEndpoint::getConstants()))) {
      throw new Exception("Argument has not been set.");
    }

    $postdata = [
      "nonce" => $this->getNonceWithIncrement(),
      "method" => $endpoint
    ];

    if (!empty($prms)) {
      $postdata = array_merge($postdata, $prms);
    }

    $postdata_query = http_build_query($postdata);
    $data = CurlWrapper::post(self::TRADE_LEVERAGE_BASE_URL, $this->getTradeHeader($postdata_query), $postdata_query);
    $data = json_decode($data);

    return $data;
  }

  private function getNonceWithIncrement()
  {
    return $this->nonce++;
  }

  private function getSign($postdata_query)
  {
    return hash_hmac("sha512", $postdata_query, $this->secret);
  }

  private function getTradeHeader($postdata_query)
  {
    return [
      "Sign: {$this->getSign($postdata_query)}",
      "Key: {$this->key}"
    ];
  }

  public static function streaming($prms, $callback)
  {

    $file_path = dirname(__FILE__) . "/vendor/autoload.php";

    if (file_exists($file_path) && is_readable($file_path)) {
      require_once $file_path;
    } else {
      throw new Exception("You can not use Streaming API.You should check including libray.");
    }

    $ws = self::STREAMING_BASE_URL . "?" . http_build_query($prms);
    $client = new Client($ws);

    while (true) {
      try {
        $json = $client->receive();
        $data = json_decode($json);
        $callback($data);
      } catch (WebSocket\ConnectionException $e) {
        $clinet = new Client($ws);
      }
    }
  }
}

class CurlWrapper
{
  public static function get($url)
  {
    $ch = curl_init();
    $options = array(
      CURLOPT_URL => $url,
      CURLOPT_HEADER => false,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_SSL_VERIFYPEER => false,
    );
    curl_setopt_array($ch, $options);
    $data = curl_exec($ch);
    curl_close($ch);

    return $data;
  }

  public static function post($url, $header, $postdata)
  {
    $ch = curl_init();
    $options = array(
      CURLOPT_URL => $url,
      CURLOPT_HEADER => false,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_POST => true,
      CURLOPT_POSTFIELDS => $postdata,
      CURLOPT_HTTPHEADER => $header,
    );
    curl_setopt_array($ch, $options);
    $data = curl_exec($ch);
    curl_close($ch);

    return $data;
  }
}
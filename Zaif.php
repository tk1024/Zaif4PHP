<?php

use WebSocket\Client;

class Zaif
{

  const PUBLIC_BASE_URL = "https://api.zaif.jp/api/1";
  const TRADE_BASE_URL = "https://api.zaif.jp/tapi";
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

  public static function pub($endpoint, $prm)
  {
    switch ($endpoint) {
      case 'last_price':
      case 'ticker':
      case 'trades':
      case 'depth':
      case 'currencies':
      case 'currency_pairs':
        break;
      default:
        throw new Exception('Argument has not been set.');
        break;
    }

    $url = self::PUBLIC_BASE_URL . '/' . $endpoint . '/' . $prm;
    $data = self::get($url);
    $data = json_decode($data);

    return $data;

  }

  public function trade($method, $prms = null)
  {
    switch ($method) {
      case 'get_info':
      case 'get_info2':
      case 'get_personal_info':
      case 'trade_history':
      case 'active_orders':
      case 'trade':
      case 'cancel_order':
      case 'withdraw':
      case 'deposit_history':
      case 'withdraw_history':
        break;
      default:
        throw new Exception('Argument has not been set.');
        break;
    }

    $postdata = array("nonce" => $this->nonce++, "method" => $method);
    if (!empty($prms)) {
      $postdata = array_merge($postdata, $prms);
    }
    $postdata_query = http_build_query($postdata);
    $sign = hash_hmac('sha512', $postdata_query, $this->secret);
    $header = array("Sign: {$sign}", "Key: {$this->key}", );
    $data = self::post(self::TRADE_BASE_URL, $header, $postdata_query);
    $data = json_decode($data);

    return $data;
  }

  public static function streaming($prms, $callback)
  {

    $file_path = dirname(__FILE__) . '/vendor/autoload.php';

    if (file_exists($file_path) && is_readable($file_path)) {
      require_once $file_path;
    } else {
      throw new Exception('You can not use Streaming API.You should check including libray.');
    }

    $ws = self::STREAMING_BASE_URL . '?' . http_build_query($prms);
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

  private static function get($url)
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

  private static function post($url, $header, $postdata)
  {
    $ch = curl_init();
    $options = array(
      CURLOPT_URL => $url,
      CURLOPT_HEADER => false,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_SSL_VERIFYPEER => false,
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

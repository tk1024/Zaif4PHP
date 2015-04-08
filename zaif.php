<?php
class Zaif {

	const PUBLIC_BASE_URL = "https://api.zaif.jp/api/1";
	const TRADE_BASE_URL = "https://zaif.jp/tapi";
	const STREAMING_BASE_URL = "ws://api.zaif.jp:8888/stream";

	private $key;
	private $secret;
	private $nonce;

	public function __construct($key, $secret) {
		$this->key = $key;
		$this->secret = $secret;
		$this->nonce = time();
	}

	public static function pub($endpoint, $prm) {

		switch ($endpoint) {
			case 'last_price':
			case 'ticker':
			case 'trades':
			case 'depth':
				break;
			default:
				throw new Exception('Argument has not been set.');
				break;
		}

		switch ($prm) {
			case 'btc_jpy':
			case 'mona_jpy':
			case 'mona_btc':
				break;
			default:
				throw new Exception('Argument has not been set.');
				break;
		}

		$url = self::PUBLIC_BASE_URL.'/'.$endpoint.'/'.$prm;
		$data = self::get($url);
		$data = json_decode( $data );

		return $data;

	}

	public function trade($method, $prms=null) {

		switch ($method) {
			case 'get_info':
			case 'trade_history':
			case 'active_orders':
			case 'trade' :
			case 'cancel_order' :
			case 'withdraw' :
				break;
			default:
				throw new Exception('Argument has not been set.');
				break;
		}

		$postdata = array( "nonce" => $this->nonce++, "method" => $method );
		if( !empty( $prms ) ) {
			$postdata = array_merge( $postdata, $prms );
		}
		$postdata_query = http_build_query( $postdata );

		$sign = hash_hmac( 'sha512', $postdata_query, $this->secret);
		$header = array( "Sign: {$sign}", "Key: {$this->key}", );

		$data = self::post( self::TRADE_BASE_URL, $header, $postdata_query );
		$data = json_decode( $data );

		return $data;
	}

	private static function get($url) {

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

	private static function post($url, $header, $postdata) {

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

<?php
class Zaif {

	const PUBLIC_BASE_URL = "https://api.zaif.jp/api/1";
	const TRADE_BASE_URL = "https://zaif.jp/tapi";

	private $key;
	private $secret;
	private $nonce;
	private $last_time;

	public function __construct($key, $secret) {
		$this->key = $key;
		$this->secret = $secret;
		$this->nonce = time();
		$this->last_api_time = 0;
	}

	public function pub($endpoint, $prm) {

		//1秒に1回以上のリクエストの場合は1秒待つ
		if( time() - $this->last_api_time < 1 ) {
			sleep(1);
		}

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
				break;
			default:
				throw new Exception('Argument has not been set.');
				break;
		}

		$url = self::PUBLIC_BASE_URL.'/'.$endpoint.'/'.$prm;
		$data = self::get($url);
		$this->last_api_time = time();
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

		//1秒に1回以上のリクエストの場合は1秒待つ
		if( time() - $this->last_api_time < 1 ) {
			sleep(1);
		}

		$postdata = array( "nonce" => $this->nonce++, "method" => $method );
		if( !empty( $prms ) ) {
			$postdata = array_merge( $postdata, $prms );
		}
		$postdata_query = http_build_query( $postdata );

		$sign = hash_hmac( 'sha512', $postdata_query, $this->secret);
		$header = array( "Sign: {$sign}", "Key: {$this->key}", );

		$data = self::post( self::TRADE_BASE_URL, $header, $postdata_query );
		$this->last_api_time = time();
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

<?php

	require_once('../class/phpQuery-onefile.php');

	// curlの実行関数
	function curl_get_content( $url )
	{
		$option = [
			CURLOPT_URL					=> $url,
			CURLOPT_HEADER				=> false,
			CURLOPT_RETURNTRANSFER	=> true,
			CURLOPT_TIMEOUT			=> 5
		];

		$ch = curl_init();
		curl_setopt_array( $ch, $option );

		$result	= curl_exec( $ch );
		$info		= curl_getinfo( $ch );
		$errorNo	= curl_errno( $ch );

		// OK以外はエラーなので空白配列を返す
		if ( $errorNo !== CURLE_OK )
		{
			return [];
		}

		// 200以外のステータスは失敗とし、空配列を返す
		if ( $info['http_code'] !== 200 )
		{
			return [];
		}

		curl_close( $ch );

		return $result;
	}

	// POSTで受け取ったurlを代入
	$url = (string)filter_input( INPUT_POST, 'url', FILTER_SANITIZE_SPECIAL_CHARS );

	// urlが空だったら停止
	if ( empty($url) )
	{
		exit;
	}

	// curlで対象ページの情報を取得
	$html = curl_get_content( $url );
	if ( empty($html) )
	{
		$title = 'ページのタイトルが取得できませんでした。';
	}
	else
	{
		$title = phpQuery::newDocument($html)->find("title")->text();
	}

	// JSON 出力
	$json = json_encode( $title );
	echo $json;

?>
<?php

	require_once('./module/basic_function.php');
	require_once('./module/config.php');
	require_once('./class/db_manager.php');

	$key	= "6LfNNFoUAAAAAFU_5KY3t8sR-hH26JnGRnY_7sEc";
	$auth	= $_POST["g-recaptcha-response"];
	$url	= "https://www.google.com/recaptcha/api/siteverify?secret={$key}&response={$auth}";

	/**
	 * ユニークな user_code 生成する
	 *
	 * @param array $all_user_code accountテーブルの全user_code
	 * @return string $token
	 */
	function createUniqueToken ( $all_user_code ) {

		$token = getRandomTxt();

		if ( empty($all_user_code) ) {
			return $token;
		}

		foreach( $all_user_code as $value ) {
			$verify = password_verify( $token, $value->user_code );
			if ( $verify ) {
				createUniqueToken( $all_user_code );
				return;
			}
		}

		return $token;

	}

	if ( isset( $_POST["action"] ) && ! empty( $auth ) ) {

		$json	= file_get_contents( $url );
		$json	= mb_convert_encoding( $json, 'UTF8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN' );
		$array= json_decode( $json , true );
		
		if ( $array['success'] === true ) {
			try {
				$dbManager	= new dbManager( CONNECTINFO );
				$result			= $dbManager->queryDB("SELECT user_code FROM account");
				$user_token = createUniqueToken( $result );
				$hash_token = ch( $user_token );
				$query			= "INSERT INTO account (user_code, mail) VALUES (?, ?)";
				$user_id		= $dbManager->insertDB( $query, array($hash_token, $user_token) );
			}
			catch (Exception $e) {
				exit($e . ': create_account');
			}
		}
		else {
			$error = '不正なアクセスです。';
		}

	}	
	else {
		$error = '不正なアクセスです。';
	}

?>

<!DOCTYPE html>
<html dir="ltr" lang="ja">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width,initial-scale=1, minimum-scale=1, maximum-scale=1, user-scalable=no">
	<meta name="format-detection" content="telephone=no,address=no,email=no">
	<title>ただのbookmark</title>
	<link rel="stylesheet" type="text/css" href="./asset/css/main.css" media="screen">
	<script src='https://www.google.com/recaptcha/api.js'></script>
</head>
<body>

<?php
	/******************************
	 * reCAPTCHAの認証成功
	******************************/
	if ( $array['success'] === true ) :
?>

	<div>
		<p>あなたのアカウントは <strong><?php echo $user_token; ?></strong> です。</p>
		<p><a href="./">bookmarkをする</a></p>
	</div>

<?php
	/******************************
	 * reCAPTCHAの認証失敗
	******************************/
	else :
?>

	<div>
		<p><strong>不正なアクセスです。</strong></p>
	</div>

<?php endif; ?>

<?php if ( $array['success'] === true ) : ?>
<script type="text/javascript">
	localStorage.setItem('user_id', '<?php echo $user_id; ?>');
	localStorage.setItem('user_token', '<?php echo $hash_token; ?>');
</script>
<?php endif; ?>

</body>
</html>
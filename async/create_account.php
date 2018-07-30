<?php

	require_once('../module/basic_function.php');
	require_once('../module/config.php');
	require_once('../class/db_manager.php');

	/**
	 * ユニークな user_code 生成する
	 *
	 * @param array $result accountテーブルの全user_code
	 * @return string $new_token
	 */
	function createUniqueToken ( $result )
	{
		$new_token = getRandomTxt();

		if ( empty($result) )
		{
			return $new_token;
		}

		foreach( $result as $value )
		{
			$verify = password_verify( $new_token, $value->user_code );
			if ( $verify )
			{
				createUniqueToken();
			}
			else
			{
				return $new_token;
			}
		}
	}

	try
	{
		$dbManager = new dbManager( CONNECTINFO );

		$result = $dbManager->queryDB("SELECT user_code FROM account");
		$data['user_token'] = createUniqueToken( $result );

		$token_hash = ch( $data['user_token'] );

		$query = "INSERT INTO account (user_code, mail) VALUES (?, ?)";
		$data['user_id'] = $dbManager->insertDB( $query, array($token_hash, $data['user_token']) );
	}
	catch (Exception $e) {
		$result = 'error: create_account';
	}

	$dbManager->unsetDB();

	$json = json_encode($data);
	echo $json;

?>
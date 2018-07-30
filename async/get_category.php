<?php

	require_once('../module/basic_function.php');
	require_once('../module/config.php');
	require_once('../class/db_manager.php');

	$add_array[] = (int)filter_input( INPUT_POST, 'user_id', FILTER_SANITIZE_FULL_SPECIAL_CHARS );

	$query = "SELECT category_id, label FROM category WHERE user_id = ?";

	try
	{
		$dbManager = new dbManager( CONNECTINFO );
		$result = $dbManager->whereDB( $query, $add_array );
	}
	catch (Exception $e) {
		$result = 'error: get_bookmark';
	}

	// DB接続アウト
	$dbManager->unsetDB();

	// JSON 出力
	$json = json_encode($result);
	echo $json;

?>
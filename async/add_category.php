<?php

	require_once('../module/basic_function.php');
	require_once('../module/config.php');
	require_once('../class/db_manager.php');

	$add_array[] = (int)filter_input( INPUT_POST, 'user_id', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
	$add_array[] = (string)filter_input( INPUT_POST, 'category', FILTER_SANITIZE_SPECIAL_CHARS );

	$query[] = "SELECT MAX(category_id) FROM category WHERE user_id = {$add_array[0]};";
	$query[] = "INSERT INTO category (user_id, label, category_id) VALUES (?, ?, ?);";

	try
	{
		$dbManager = new dbManager( CONNECTINFO )
		;
		$id = $dbManager->queryDB( $query[0] );
		foreach( $id[0] as $value ){ $id = $value; }
		$add_array[] = intval($id) + 1;

		$result = $dbManager->insertDB( $query[1], $add_array );
	}
	catch (Exception $e) {
		$result = 'error: add_category';
	}

	// DB接続アウト
	$dbManager->unsetDB();

	// JSON 出力
	$json = json_encode($result);
	echo $json;

?>
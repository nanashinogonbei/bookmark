<?php

	require_once('../module/basic_function.php');
	require_once('../module/config.php');
	require_once('../class/db_manager.php');

	$add_array[] = (int)filter_input( INPUT_POST, 'user_id', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
	$add_array[] = (string)filter_input( INPUT_POST, 'url', FILTER_SANITIZE_URL );
	$add_array[] = (string)filter_input( INPUT_POST, 'title', FILTER_SANITIZE_SPECIAL_CHARS );
	$add_array[] = 0;
	$add_array[] = 0;
	$add_array[] = (int)filter_input( INPUT_POST, 'view', FILTER_SANITIZE_NUMBER_INT );
	
	$query = "INSERT INTO `unit` (`user_id`, `url`, `title`, `category_id`, `order`, `view`) VALUES (?, ?, ?, ?, ?, ?)";

	try
	{
		$dbManager = new dbManager( CONNECTINFO );
		$result = $dbManager->insertDB( $query, $add_array );
	}
	catch (Exception $e) {
		$result = 'error: add_bookmark';
	}

	// DB接続アウト
	$dbManager->unsetDB();

	// JSON 出力
	$json = json_encode($result);
	echo $json;

?>
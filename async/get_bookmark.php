<?php

	require_once('../module/basic_function.php');
	require_once('../module/config.php');
	require_once('../class/db_manager.php');

	$add_array[] = (int)filter_input( INPUT_POST, 'user_id', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
	$add_array[] = filter_input( INPUT_POST, 'category_id', FILTER_SANITIZE_FULL_SPECIAL_CHARS );

	$add_array[1] = explode( ',', $add_array[1]);

	$query = "SELECT * FROM `unit` WHERE `user_id` = ? AND `category_id` = ? ORDER BY `order` ASC";

	try
	{
		$dbManager = new dbManager( CONNECTINFO );
		$length = count( $add_array[1] );
		for ( $i = 0; $i < $length; $i++ ) {
			$result[] = $dbManager->whereDB( $query, array( $add_array[0], $add_array[1][$i] ) );
		}
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
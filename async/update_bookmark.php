<?php

	require_once('../module/basic_function.php');
	require_once('../module/config.php');
	require_once('../class/db_manager.php');

	$arrays;
	$i = 0;

	foreach ( $_POST as $values ) {
		$arrays[$i++] = explode( ',', $values );
	}

	$i = 0;
	$data = array();
	/*
	順番
		[0] title
		[1] url
		[2] view
		[3] user_id
		[4] category_id
		[5] order
		[6] id
	*/
	foreach ( $arrays as $values ) {
		$data[$i++] = array(
			'title'				=> filter_var( $values[0], FILTER_SANITIZE_FULL_SPECIAL_CHARS ),
			'url'					=> filter_var( $values[1], FILTER_SANITIZE_FULL_SPECIAL_CHARS ),
			'view'				=> (int)filter_var( $values[2], FILTER_SANITIZE_NUMBER_INT ),
			'user_id'			=> (int)filter_var( $values[3], FILTER_SANITIZE_NUMBER_INT ),
			'category_id'	=> (int)filter_var( $values[4], FILTER_SANITIZE_NUMBER_INT ),
			'order'				=> (int)filter_var( $values[5], FILTER_SANITIZE_NUMBER_INT ),
			'id'					=> (int)filter_var( $values[6], FILTER_SANITIZE_NUMBER_INT )
		);
	}

	try
	{
		$dbManager = new dbManager( CONNECTINFO );
		$length = count( $data );
		for ( $i = 0; $i < $length; $i++ ) {
			$result[$i] = $dbManager->updateDB( 'unit', $data[$i] );
		}
	}
	catch (Exception $e) {
		$result = 'error: get_bookmark';
	}

	// DB接続アウト
	$dbManager->unsetDB();
	// JSON 出力
	$json = json_encode( $result );
	echo $json;

?>
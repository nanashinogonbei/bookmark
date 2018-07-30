<?php

require_once('./module/basic_function.php');
require_once('./module/config.php');
require_once('./class/db_manager.php');

use PHPUnit\Framework\TestCase;

class StackTest extends TestCase
{
    public function testPushAndPop()
    {

			$dsn = sprintf(
				'mysql:host=%s;dbname=%s;port=%d;charset=utf8;',
				CONNECTINFO['host'],
				CONNECTINFO['dbname'],
				CONNECTINFO['port']
			);
			$pdo = new PDO( $dsn, CONNECTINFO['user'], CONNECTINFO['pass'] );

			$query = "INSERT INTO unit ('user_id', 'url', 'title', 'category_id', 'order', 'view') VALUES (?, ?, ?, ?, ?, ?);";
			$add_array[] = 1;
			$add_array[] = 'http://www.ok.com';
			$add_array[] = 'title';
			$add_array[] = 0;
			$add_array[] = 0;
			$add_array[] = 0;

			$sth = $pdo->prepare( $query );
			$result = $sth->execute( array(1, 'ok', 'title', 1, 1, 1) );
			
			//$result = $dbManager->insertDB( $query, array(1, 'http://www.hoge.com', 'title', 0, 0, 0) );
			//$this->assertSame( 'foo', $result );
			var_dump( $result );
			//$pdo->unsetDB();
    }
}

?>
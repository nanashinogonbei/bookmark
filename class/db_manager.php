<?php

class dbManager
{
	protected $db;


	public function __construct( $connectInfo )
	{
		$this->initDB( $connectInfo );
	}

	public function returnPDO()
	{
		return $this->db;
	}

	public function initDB( $connectInfo )
	{
		$dsn = sprintf(
			'mysql:host=%s;dbname=%s;port=%d;charset=utf8;',
			$connectInfo['host'],
			$connectInfo['dbname'],
			$connectInfo['port']
		);
		try
		{
			$this->db = new PDO( $dsn, $connectInfo['user'], $connectInfo['pass'] );
			//$this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
		}
		catch (Exception $e) {
			echo 'error' . $e->getMesseage;
			die();
		}
	}

	/**
	 * PDOでqueryを実行する
	 * 結果を配列で返す
	 *
	 * @param string $query sql文
	 * @return array
	 */
	public function queryDB( $query )
	{
		return $this->db->query($query)->fetchAll(PDO::FETCH_CLASS);
	}

	/**
	 * PDOでsql文 SELECT WHEREを実行する
	 * 結果を配列で返す
	 *
	 * @param string $table_name
	 * @param array $items 連想配列['カラム名'] = 値
	 * @return array
	 */
	public function whereDB( $query, $values )
	{
		$sth = $this->db->prepare( $query );
		$sth->execute( $values );
		$result = $sth->fetchAll(PDO::FETCH_CLASS);
		return $result;
	}

	public function insertDB( $query, $values )
	{
		$sth = $this->db->prepare( $query );
		$result = $sth->execute( $values );
		if ( $result === true )
		{
			$result = $this->db->lastInsertId();
		}
		return $result;
	}


	public function updateDB( $table, $items )
	{
		$changes = '';
		$count = count($items);
		$i = 0;
		$params = [];

		foreach ( $items as $key => $value )
		{
			if ( $key !== 'id' ) {
				if ( $key !== 'order' ) { $changes .= "`${key}`=:${key}, "; }
				if ( $key === 'order' ) { $changes .= "`${key}`=:${key}"; }
			}
			$i++;
		}
		$sql = "UPDATE `${table}` SET ${changes} WHERE `${table}`.`id`=:id";

		$sth = $this->db->prepare( $sql );
		$result = $sth->execute($items);

		return $result;

	}


	public function addMetaTable ($name)
	{
		$sql = "CREATE TABLE IF NOT EXISTS meta_${name} "
		. "("
		. "ID INT(2) auto_increment primary key, "
		. "label text NOT NULL"
		. ");";
		$sth = $this->db->prepare($sql);
		$sth->execute();

		$sql = "ALTER TABLE design_list ADD ${name} varchar(20)";
		$sth = $this->db->prepare($sql);
		$sth->execute();
	}


	public function lastID( $table_name )
	{
		$sql = "SELECT LAST_INSERT_ID() FROM {$table_name}";
		$last_id = $this->queryDB($sql);
		return $last_id;
	}

	public function lastA_I( $table_name )
	{
		$sql = "SHOW TABLE STATUS LIKE '{$table_name}'";
		$last_id = $this->queryDB($sql);
		return $last_id[0]->Auto_increment;
	}

	public function unsetDB () {
		unset( $this->db );
	}

	public function echoMemory () {
		$mem     = memory_get_usage() / (1024 * 1024);
		$peakmem = memory_get_peak_usage() / (1024 * 1024);
		echo "<p class='memory'>Memory: {$mem}MB / Peak Memory: {$peakmem}MB</p>";
	}

}

?>
<?php
/**
 * @author Rustam Ibragimov
 * @mail Rustam.Ibragimov@softline.ru
 * @date 26.06.13
 */
namespace Myzend\Model;

use Zend\Db\Sql\Sql;

class MyzendTable
{

	public function __construct($dbad)
	{
		$this->adapter = $dbad;
	}

	public function fetchAll($table)
	{
		$adapter = $this->adapter;
		$sql = new Sql($adapter);
		$select = $sql->select();
		$select->from($table);
		$selectString = $sql->getSqlStringForSqlObject($select);
		$results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
		return $results;
	}

	public function addOne($table, $aAdd) {
		$adapter = $this->adapter;
		$sql = new Sql($adapter);
		$insert = $sql->insert($table);
		$insert->values($aAdd);

		$statement = $sql->prepareStatementForSqlObject($insert);
		$results = $statement->execute();
		return $results;
	}
}

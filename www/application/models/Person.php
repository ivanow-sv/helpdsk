<?php

/**
 * @author zlydden
 *
 */
class Person extends Zend_Db_Table
{

	private $dbAdapter;
	private $table;
	private $tablePrivateInfo;

	public function __construct()
	{
		$this->dbAdapter = $this->getDefaultAdapter();
		//		$this->dbAdapter=$db;
		$this->table='personprocess';
		$this->tablePrivateInfo='personal';

	}



	/**
	 * добавить запись в журнал оперпций с субъектом
	 * @param integer ID пользователя
	 * @param integer ID операции
	 * @param integer ID документа
	 * @param string коментарий
	 * @param string имя параметра
	 * @param string значение параметра
	 * @param integer ID пользователя-автора
	 * @return integer id записи
	 */
	public function addRecord($userid,$operation,$documentid=0,$comment='',$param,$value='',$author=0)
	{
		$curdate=date("Y-m-d H:i:s");
		$data=array();
		$data["userid"]=$userid;
		$data["operation"]=$operation;
		$data["createdate"]=$curdate;
		$data["paramName"]=$param;
		$data["paramValue"]=$value;
		$data["author"]=$author;
		if($documentid!=0) $data["documentid"]=$documentid;
		if($comment!=='') $data["comment"]=$comment;
		$this->dbAdapter->insert($this->table,$data);
		return $this->dbAdapter->lastInsertId();
	}

	/** ввод записи из массива - проверка не производится чо там
	 * @param array $array
	 * @return array
	 */
	public function addRecordByArray($array)
	{
		$array["createdate"]=date("Y-m-d H:i:s");
		$this->dbAdapter->insert($this->table,$array);
		return $this->dbAdapter->lastInsertId();
	}

	/**
	 * Изменение записей - WHERE не проверятся!!!!
	 * @param array $where
	 * @param array $data
	 * @return integer затронуто записей
	 */
	public function updateRecords($where,$data)
	{
		$aff=$this->dbAdapter->update($this->table,$data,$where);
		return $aff;
	}
}
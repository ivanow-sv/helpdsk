<?php
class Sprav extends Zend_Db_Table
{
	private $table;
	private $db;

	public function __construct($tablename)
	{
		$db = $this->getDefaultAdapter();
		$this->db=$db;
		$this->table=$tablename;

	}

	public function getTableName()
	{
		return $this->table;
	}

	public function setTableName($tablename)
	{
		$this->table=$tablename;
	
	}
	
	public function about()
	{
		$q="SELECT * FROM ".$this->table;
		//    	echo $q;
		$result=$this->db->fetchRow($q);
		return $result;
	}

	public function aboutSave($data,$id=1)
	{
		$where="id = ".$id;
		$this->db->update($this->table,$data,$where);
		return;
	}

	/**
	 * список типовой, для таблиц ID - TITLE
	 */
	public function typicList()
	{
		$q="SELECT id,title FROM ".$this->table;
		$q.=" ORDER BY title ASC";
		$result=$this->db->fetchAll($q);
		return $result;
	}

	public function typicChange($id,$title)
	{
		$where="id = ".$id;
		$data=array("title"=>$title);
		$this->db->update($this->table,$data,$where);
		return;
			
	}

	public function typicAdd($title)
	{
		$data=array("title"=>$title);
		$affecred=$this->db->insert($this->table,$data);
		$insertId=$this->db->lastInsertId($this->table,"id");
		return 	$insertId;
	}

	public function typicDel($id)
	{
		$where="id=".$id;
		$affected=$this->db->delete($this->table,$where);
		return 	$affected;
	}

	/// --------------------------------
	// для остальных таблиц

	public function otherList($table='',$where='')
	{
		if ($table==='' || is_null($table)) $table =$this->table;
		if ($where==='' || is_null($where)) $where='';
		else $where =" WHERE ".$where;
		//		else $where='';

		$q="SELECT * FROM ".$table.$where;
		//		echo $q; die();
		$result=$this->db->fetchAll($q);
		return $result;
	}

	/**
	 * @param array BIND $data
	 * @return integer
	 */
	public function otherAdd($data)
	{
		$affecred=$this->db->insert($this->table,$data);
		$insertId=$this->db->lastInsertId($this->table,"id");
		return $insertId;
	}


	public function otherChange($id,$data)
	{
		$where="id = ".$id;
		$this->db->update($this->table,$data,$where);
		return;
	}



}
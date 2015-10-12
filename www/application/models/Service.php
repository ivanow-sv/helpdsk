<?php

class Service extends Zend_Db_Table
{

	private $db;
	//	private $dekanat;
	private $person;
	private $user;
	private $role;


	public function __construct()
	{
		$db = $this->getDefaultAdapter();
		$this->db=$db;
		Zend_Loader::loadClass('Person');
		Zend_Loader::loadClass('Users');
		Zend_Loader::loadClass('Roles');
		$this->person=new Person();
		$this->user=new Users();
		$this->role=new Roles();

	}



	/** выполнить запрос
	 * @param string $sql
	 * @return Zend_Db_Statement_Interface
	 */
	public function execSql($sql,$bind=array())
	{
		try
		{
			return $this->db->query($sql,$bind);

		}
		catch (Zend_Exception $e)
		{
			return $e->getMessage();
		}
	}

	/**
	 * выполнгить запрос и вернутть результаты
	 * @param string $sql
	 * @param unknown_type $bind
	 * @return Ambigous <multitype:, string, boolean, mixed>
	 */
	public function fetchSql($sql,$bind=array())
	{
		try
		{

			return $this->db->fetchAll($sql,$bind);

		}
		catch (Zend_Exception $e)
		{
			return $e->getMessage();
		}
	}



	public function roleChange($userid,$newrole)
	{
		try
		{
			$result=$this->user->setRole($userid,$newrole);
			return array("state"=>"OK","text"=>$result);

		}
		catch (Zend_Exception $e)
		{
			return array("state"=>"FAIL","text"=>$e->getMessage());
		}

	}


	public function createPrivateInfo($userid,$info)
	{
		return $this->user->personalInfoCreate($userid,$info);
	}

	public function personalInfoChange($userid,$info)
	{
		try
		{
			return $this->user->personalInfoChange($userid,$info);
		}
		catch (Zend_Exception $e)
		{
			return array("state"=>"FAIL","text"=>$e->getMessage());
		}

	}


	public function insert($table,$data)
	{
		$res=array();
		try
		{
			$this->db->insert($table, $data);
			$res["status"]=true;
		}
		catch (Zend_Exception $e)
		{
			$res["status"]=false;
			$res["errorMsg"]=$e->getMessage();
		}
		return $res;
		;
	}

	public function update($table,$data, $where)
	{

		return $this->db->update($table,$data, $where);
		;
	}



	/**
	 * @param integer $id
	 * @return string
	 */
	public function annotations_getTextByResID($id)
	{
		$select=$this->db->select()->from(array("annot"=> "acl_res_annot"),"annotation");
		$select->where("annot.resid = ".$id);

		$stmt = $this->db->query($select);
		$result = $stmt->fetchColumn();
		return $result;
	}

	/**
	 * @param string $text
	 * @param integer $id
	 * @return unknown
	 */
	public function annotations_newText($text,$resid)
	{
		$data["annotation"]=$text;
		$data["resid"]=$resid;
		try
		{
			$result=$this->db->insert("acl_res_annot",$data);
			return array("state"=>"OK","text"=>$result);
		}
		catch (Zend_Exception $e)
		{
			return array("state"=>"FAIL","text"=>$e->getMessage());
		}

	}

	/**
	 * @param string $text
	 * @param integer $id
	 * @return unknown
	 */
	public function annotations_changeText($text,$resid)
	{
		$data["annotation"]=$text;
		try
		{
			$result=$this->db->update("acl_res_annot",$data,"resid=".$resid);
			return array("state"=>"OK","text"=>$result);
		}
		catch (Zend_Exception $e)
		{
			return array("state"=>"FAIL","text"=>$e->getMessage());
		}


	}

}
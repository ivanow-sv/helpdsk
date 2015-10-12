<?php
		Zend_Loader::loadClass('Zend_Form');
		Zend_Loader::loadClass('Formochki');

class My_Helper_Forms extends Zend_Controller_Action_Helper_Abstract
{

	private $db; // копия default db adapter 
	private	$confirmWord="УДАЛИТЬ";
	
	public function setDBadapter($adapter)
	{
		$this->db=$adapter;
	}
	
	
	/**
	 * форма пользователя, минимальная 
	 * @return Formochki
	 */
	public function userInfo() 
	{
		$form=new Formochki();
		$form->setAttrib('name','useredit');
		$form->setAttrib('id','useredit');
		$form->setMethod('POST');
		$form->addElement("hidden","userid");
		$form->getElement("userid")->addValidator("Digits",true)->setRequired(true);
		// фамилия
		$form->addElement("text","family",array("class"=>"typic_input"));
		$form->getElement("family")
		->addValidator("Alpha",true,array('allowWhiteSpace' => true))
		->setRequired(true)
		->setDescription("Фамилия")		;
		//	имя
		$form->addElement("text","name",array("class"=>"typic_input"));
		$form->getElement("name")
		->setRequired(true)
		->addValidator("Alpha",true,array('allowWhiteSpace' => true))
		->setDescription("Имя");
		//	отчество
		$form->addElement("text","otch",array("class"=>"typic_input"));
		$form->getElement("otch")
		->setRequired(true)
		->addValidator("Alpha",true,array('allowWhiteSpace' => true))
		->setDescription("Отчество");
		// логин
		$form->addElement("text","login",array("class"=>"typic_input"));
		$form->getElement("login")
		->setRequired(true)
		->addValidator("StringLength",true,array('min' => 2))
// 		->addValidator("Alnum",true,array('allowWhiteSpace' => false))
		->addValidator("Regex",true,array('pattern' => '/^[a-zA-Z0-9]+$/'))
		->setDescription("Login");
		// комментарий
		$form->addElement("textarea","comment",array("class"=>"medinput"));
		$form->getElement("comment")
// 		->setRequired(true)
		->addValidator("Alnum",true,array('allowWhiteSpace' => true))
		->setDescription("Комментарий");
		// отключен/включен
		$form->addElement("radio","disabled");
		$form->getElement("disabled")
		->setRequired(true)
		->addValidator("NotEmpty",true,array("integer","zero"))
		->addMultiOption(0, "Включен")
		->addMultiOption(1, "Выключен")
		->setDescription("Вкл./откл.");
		// пароль
		$form->addElement("password","pass",array("class"=>"medinput"));
		$form->getElement("pass")
// 		->setRequired(true)
		->addValidator("Alnum",true,array('allowWhiteSpace' => false))
		->addValidator("Identical",true,array("token"=>"passconfirm"))
		->setDescription("Пароль");
		// подтверждение пароля ??
		// пароль
		$form->addElement("password","passconfirm",array("class"=>"medinput"));
		$form->getElement("passconfirm")
		// 		->setRequired(true)
		->addValidator("Alnum",true,array('allowWhiteSpace' => false))
		->addValidator("Identical",true,array("token"=>"pass"))
		->setDescription("Подтверждение пароля");
		
		$form->addElement("text","email",array("class"=>"medinput"));
		$optionsEmail=array(
				"domain"=>true,
				);
		$form->getElement("email")
		->addValidator("EmailAddress",true,$optionsEmail)
		;
		
		// reset
		$form->addElement("reset","RES",array(
		"class"=>"apply_text"		
		));
		$form->getElement("RES")->setName("Вернуть");
		// submit
		$form->addElement("submit","OK",array(
		"class"=>"apply_text"		
		));
		$form->getElement("OK")->setName("Сохранить");
				
		return $form;
	}
	

	/**
	 * построить список выбора
	 * @param string $elemName
	 * @param array $src data ID=>value
	 * @param integer $defaultValue selected option
	 * @return Zend_Form_Element_Select
	 */
	public  function createSelectList($elemName,$src,$nullTitle="",$selected=false)
	{
		Zend_Loader::loadClass('Zend_Form_Element_Select');
		// ОБЛОМ! зенд тянект тока двухуровневое дерево :(
	
		$result = new Zend_Form_Element_Select($elemName);
		if ($nullTitle!=='') $result->addMultiOption(0,$nullTitle);
	
		//		$result->addMultiOptions($src);
		foreach ($src as $key=>$value)
		{
			$result ->addMultiOption($key,$value);
		}
		// выбранное значение SELECTED
		if ($selected !==false) $result  ->setValue($selected);
		$result  ->removeDecorator('Label');
		$result  ->removeDecorator('HtmlTag');
		return $result;
	}

	public function add()
	{
		$form=new Formochki();
		$form->setAttrib('name','add');
		$form->setAttrib('id','add');
		$form->setMethod('POST');
// 		$form->setAction($this->view->baseUrl
// 				.'/'.$this->view->currentModuleName
// 				.'/'.$this->_request->getControllerName()
// 				.'/'.'add'
// 		);
		$textOptions=array('class'=>'typic_input');
	
		// если указано - то создается дочерний
		$form->addElement("hidden","id");
		$form->getElement("id")
		->setRequired(true)
		->addValidator("Digits",true)
		->addValidator("NotEmpty",true,array("integer","zero"))
		->setDescription("id");
	
	
		$form->addElement("text","title",array("class"=>"longinput"));
		$form->getElement("title")
		->setRequired(true)
		->addValidator("NotEmpty",true)
		->setDescription("Наименование");
	
		$form->addElement("submit","OK",array(
				"class"=>"apply_text"
		));
		$form->getElement("OK")->setName("Добавить");
		return $form;
	
		;
	}

	public function del()
	{
		$form=new Formochki();
		$form->setAttrib('name','del');
		$form->setAttrib('id','del');
		$form->setMethod('POST');
		$textOptions=array('class'=>'typic_input');
	
// 		$_restr=$this->_model->getRestricted();
		$form->addElement("hidden","id");
		$form->getElement("id")
		->setRequired(true)
		->addValidator("NotEmpty",true)
		// 		->addValidator("InArray",true,array("haystack"=>array_keys($_restr),"strict"=>true))
		->setDescription("id");
	
		$form->addElement("text","confirm",$textOptions);
		$form->getElement("confirm")
		->setRequired(true)
		->addValidator("NotEmpty",true)
		->addValidator("Identical",true,array('token'=>$this->confirmWord))
		->setDescription("Слово-подтверджение");
	
		$form->addElement("submit","OK",array(
				"class"=>"apply_text"
		));
		$form->getElement("OK")->setName("Удалить");
		return $form;
	
	
	}

	/**
	 * @param array $destList список получателей
	 * @return Formochki
	 */
	public function move($destList)
	{
		
		$form=new Formochki();
		$form->setAttrib('name','move');
		$form->setAttrib('id','move');
		$form->setMethod('POST');

		$form->addElement("hidden","id");
		$form->getElement("id")
		->setRequired(true)
		->setIsArray(true)
		->addValidator("NotEmpty",true)
		->addValidator("Digits",true);
		
		$dest_idz=array_keys($destList);
		$_dest=$this->createSelectList("destination",$destList);
		$form->addElement($_dest);
		$form->getElement("destination")
		->setRequired(true)
		->addValidator("NotEmpty",true)
		->addValidator("Digits",true)
		->addValidator("InArray",true,array($dest_idz))
		->setDescription("Группа")
		;

		$form->addElement("submit","OK",array(
				"class"=>"apply_text"
		));
		$form->getElement("OK")->setName("Применить");
		return $form;
	}
	
	
	private function getSpravTypic($tablename)
	{
		$sql="SELECT `id` AS `key`, `title` AS `value` FROM ".$tablename;
		$result=$this->db->fetchPairs($sql);
		return $result;
	
	}
	
}
<?php
class Service_BdpatchesController extends Zend_Controller_Action
{
	protected 	$session;
	protected 	$baseLink;
	protected	$redirectLink; // ссылка в этот модуль/контроллер
	private 	$_model;
	private 	$hlp; // помощник действий Typic
	private 	$logPath; // путь к логам
	private 	$patchDescription="descript.ion"; // имя фала-описания патчей, находится в папка с логами
	private 	$patchCurrent=''; // текущего патча
	//	private 	$patchLog="descript.ion"; // имя фала-описания с логами

	private $_num_len=3; // кол-во знаков в № зачетки
	private $_list1="|";
	private $_listFil="--";
	private $_author; // пользователь шо щаз залогинен


	function init()
	{
		// выясним название текущего модуля для путей в ссылках
		$currentModule=$this->_request->getModuleName();
		$this->view->currentModuleName=$currentModule;
		$this->view->baseUrl = $this->_request->getBaseUrl();
		$this->view->currentController = $this->_request->getControllerName();
		$this->baseLink=$this->_request->getBaseUrl()."/".$currentModule."/".$this->_request->getControllerName();
		$this->view->baseLink=$this->baseLink;
		$this->redirectLink=$this->_request->getModuleName()."/".$this->_request->getControllerName();
		$this->hlp=$this->_helper->getHelper('Typic');
		$this->logPath=APPLICATION_PATH."/logs/patches";
		$this->patchCurrent=$this->_request->getActionName();
		Zend_Loader::loadClass('Service');

		// @TODO из реестра взять с каким факультетом работает данный пользователь
		// @TODO а еси может со всем факультетами работать? админ например

		$groupEnv=Zend_Registry::get("groupEnv");
		//		$this->currentFacultId=$roleEnv['currentFacult'];
		$this->_model=new Service();

		$moduleTitle=Zend_Registry::get("ModuleTitle");
		$modContrTitle=Zend_Registry::get("ModuleControllerTitle");
		$this->view->title=$moduleTitle
		.". ".$modContrTitle.'. ';
		$this->view->addHelperPath('./application/views/helpers/','My_View_Helper');

		Zend_Loader::loadClass('Zend_Session');
		Zend_Loader::loadClass('Zend_Form');
		Zend_Loader::loadClass('Formochki');
		Zend_Loader::loadClass('Zend_Filter_StripTags');
		$this->session=new Zend_Session_Namespace('my');
		$ajaxContext = $this->_helper->getHelper('AjaxContext');
		//		$ajaxContext ->addActionContext('formchanged', 'json')->initContext('json');
		//		$ajaxContext ->addActionContext('freelist', 'json')->initContext('json');
		//		$ajaxContext ->addActionContext('zachchange', 'json')->initContext('json');
		$ajaxContext ->addActionContext('logshow', 'json')->initContext('json');
		$this->view->headScript()->appendFile($this->_request->getBaseUrl().'/public/scripts/service.js');
		//		$this->view->headScript()->appendFile($this->_request->getBaseUrl().'/public/styles/dekanat.css');
		//		Zend_Controller_Action_HelperBroker::addPrefix('My_Helper');
		$this->_author=Zend_Auth::getInstance()->getIdentity();
	}

	// имена патчей начинаются с "patch"
	function indexAction()
	{
		// применены ли патчи
		// список файлов в директории с логами. Включая файл описаний
		$logList=scandir($this->logPath);
		// ключ = файл
		$logList=array_flip($logList);

		// описания патчей
		$_description=file($this->logPath."/".$this->patchDescription);
		$description=array();
		foreach ($_description as $line)
		{
			$_line=explode("\t",$line);
			$description[$_line[0]]=$_line[1];
		}
		//		echo "<pre>".print_r($logList,true)."</pre>";

		// построим меню-список Actions
		// методы класса-родителя
		$actionsParent=get_class_methods(get_parent_class($this));
		// методы класса
		$actionsList=get_class_methods($this);
		// методы исключительнло ЭТОГО контроллера (без "init" )
		//		$diff=array();
		$diff=array_diff($actionsList,$actionsParent);
		$actions=array();
		foreach ($diff as $variable)
		{
			$_action=str_replace("Action",'',$variable);
			// со словом "patch"
			if (strpos($_action,"patch")!==false)
			{
				//				// лог
				$actions[$_action]["log"]=isset($logList[$_action.".log"])
				?	true
				:	'';
				// описание
				$actions[$_action]["description"]=isset($description[$_action])
				?	$description[$_action]
				:	"";
			}
			;
		}
		$this->view->list=$actions;
		//		echo "<pre>".print_r($actions,true)."</pre>";

	}

	function logshowAction() {
		// если AJAX
		if ($this->_request->isXmlHttpRequest())
		{
			$patchName=$this->_request->getParam("patch",'');
			if (empty($patchName)) return;
			$logFilePath=$this->logPath.DIRECTORY_SEPARATOR.$patchName.".log";
			// очистим вывод
			$this->view->clearVars();
			$this->view->baseLink=$this->baseLink;
			$this->view->baseUrl = $this->_request->getBaseUrl();
			// лог
			$out["log_".$patchName]=isset($logFilePath)
			?	nl2br(file_get_contents($logFilePath))
			:	'';
			$this->view->out=$out;
		}
		;
	}



	/**
	 * открытие лога
	 */
	private function logstart()
	{
		$f=$this->logPath."/".$this->patchCurrent.".log";
		// запишем в него шо начали работу
		file_put_contents($f,"\n========= ".date("Y-m-d H:i:s, D")." =========\n",FILE_APPEND);
		file_put_contents($f,date("Y-m-d H:i:s, D")."\t Начало\n",FILE_APPEND);

	}

	/** пишем лог,
	 * @param string $text текст
	 */
	private function logwrite($text)
	{
		$f=$this->logPath."/".$this->patchCurrent.".log";
		if (is_array($text))
		{
			foreach ($text as $t)
			{
				file_put_contents($f,date("Y-m-d H:i:s, D")."\t ".$t." \n",FILE_APPEND);
			}
		}
		else {
			file_put_contents($f,date("Y-m-d H:i:s, D")."\t ".$text." \n",FILE_APPEND);
		}
	}

	/**
	 *  закрытие лога
	 */
	private function logend()
	{
		$f=$this->logPath."/".$this->patchCurrent.".log";
		file_put_contents($f,date("Y-m-d H:i:s, D")."\t Завершение\n",FILE_APPEND);
		//		chmod($f,0644);
		//		chown($f,"zlydden");
		//		chgrp($f,"zlydden");
	}

}
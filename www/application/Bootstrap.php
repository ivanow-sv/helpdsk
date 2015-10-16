<?php 
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
	public function _initAutoloader()
	{
		//// Create an resource autoloader component
		$autoloader = new Zend_Loader_Autoloader_Resource(array(
            'basePath'    => APPLICATION_PATH,
            'namespace' => 'My'
            ));

            // Add some resources types
            $autoloader->addResourceTypes(array(
            'models' => array(
                'path'           => 'models',
                'namespace' => ''   
                ),
                ));

                // Return to bootstrap resource registry
                return $autoloader;
	}

		protected function _initRoutes()
		{
			$router = $this->bootstrap('frontController')->getResource('frontController')->getRouter();
			// для разработке на DENWER
			$hostnameRoute = new Zend_Controller_Router_Route_Hostname(
					'help.local',
					array(
							'module'		=>	"helpdesk",
							'controller'	=>	'index'
					)
			);
			$plainPathRoute = new Zend_Controller_Router_Route_Static(
					'',
					array(
							'module'		=>	"helpdesk"
							)
					);
			
			// PRODUCTION
			$hostname2Route = new Zend_Controller_Router_Route_Hostname(
					'help.academy21.ru',
					array(
							'module'		=>	"helpdesk",
							'controller'	=>	'index'
					)
			);
			$plainPath2Route = new Zend_Controller_Router_Route_Static(
					'',
					array(
							'module'		=>	"helpdesk"
							)
					);
		
			
			$router->addRoute('help', $hostnameRoute->chain($plainPathRoute));
			$router->addRoute('help2', $hostname2Route->chain($plainPath2Route));
			return $router;
		}

	

	public function _initAuth()
	{
		$this->bootstrap('Db');
		//		$db= $this->getResource('Db');

		$auth = Zend_Auth::getInstance();


		Zend_Loader::loadClass('My_Plugin_Auth');
		$this->bootstrap('frontController');
		$front = $this->getResource('frontController');

		// берем объекты доступа
		//		$this->bootstrap('Acl');
		//		$acl= $this->getResource('Acl');

		$front->registerPlugin(new My_Plugin_Auth($auth));
		return $auth;
	}


	}
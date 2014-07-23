<?php
	class TestDBManager extends DBManager
	{
		function __construct(DBPool $dbPool = null)
		{
			parent::__construct(null, null, false);

			$this->pool = TestDBPool::me();

			$this->connectToAllDB();
		}

		/**
		 * Fill DB with data
		 * @return static
		 */
		public function fillDB()
		{
			$this->fillCommonData();

			return parent::fillDB();
		}


		/**
		 *  Fill in common data
		 */
		public function fillCommonData()
		{
			/**
			 * Access resources: controllers & actions
			 */
			$controllersAndActionsMeta = array(
				'index' => array(
					'title' => 'Index Controller',
					'actions' => array(
						'index' => array(
							'title' => 'Index Action - Index Controller'
						)
					)
				),
			);
			$accessResourcesIdentityMap = array();
			$baseAccessResource =
				BackendAccessResource::create()->
				setIsActive(true);
			foreach ($controllersAndActionsMeta as $name => $controllerData) {

				$controller = clone $baseAccessResource;
				$controller->
				setType(EnumBackendAccessResourceType::controller())->
				setName($name)->
				setTitle($controllerData['title']);
				$controller->dao()->take($controller);

				$actionsList = array();
				if (!empty($controllerData['actions'])) {
					foreach ($controllerData['actions'] as $actionName => $actionData) {
						$action = clone $baseAccessResource;
						$action->
						setParent($controller)->
						setType(EnumBackendAccessResourceType::controllerAction())->
						setName($actionName)->
						setTitle($actionData['title']);

						$action->dao()->take($action);
						$actionsList[$actionName] = $action;
					}


				}

				$accessResourcesIdentityMap[$name] = array(
					'controller' => $controller,
					'actions' => $actionsList
				);

			}


			/**
			 * Roles
			 */
			$roles = array(
				'admin' => array(
					'name' => 'admin',
					'controllers' => array(
						'index' => array('index') //All actions
					)
				),
				'manager' => array(
					'name' => 'manager',
					'controllers' => array(
						'index' => array() //All actions
					)
				)
			);

			$baseRole = BackendUserRole::create();
			foreach ($roles as $name => $roleData) {
				$role = clone $baseRole;
				$role->setName($name);
				$role->dao()->take($role);

				if (!empty($roleData['controllers'])) {
					$permissionsDao = $role->getPermissions();
					$permissions = array();
					foreach ($roleData['controllers'] as $controllerName => $actions) {
						$permissions[] = $accessResourcesIdentityMap[$controllerName]['controller'];
						foreach ($actions as $actionName) {
							$permissions[] = $accessResourcesIdentityMap[$controllerName]['actions'][$actionName];
						}
					}

					$permissionsDao->fetch()->setList($permissions)->save();
				}
			}


			/**
			 * Backend Users
			 */
			//admin
			$backendUserAdmin =
				BackendUser::create()->
				setIsSuperAdmin(true)->
				setLogin('admin')->
				setPassword(md5('12345'))->
				setEmail('admin@onphp-inception.com')
			;
			$backendUserAdmin->dao()->take($backendUserAdmin);

			return $this;
		}
	}
<?php
/**
 * @author Eric I. Gorbikov <ernest.gorbikov@gmail.com>
 * @copyright 2014 Eric I. Gorbikov <ernest.gorbikov@gmail.com>
 */

class BaseCRUDControllerTestCase extends PHPUnit_Framework_TestCase {

	/**
	 * @test
	 */
	public function created_allMandatoryActions_mustExistAsMethods()
	{
		$controller = $this->makeInitiatedController();

		$this->assertTrue(method_exists($controller, 'listAction'), 'listAction must exist');
		$this->assertTrue(method_exists($controller, 'viewAction'), 'viewAction must exist');
		$this->assertTrue(method_exists($controller, 'editAction'), 'editAction must exist');
		$this->assertTrue(method_exists($controller, 'saveAction'), 'saveAction must exist');
		$this->assertTrue(method_exists($controller, 'createAction'), 'createAction must exist');
		$this->assertTrue(method_exists($controller, 'updateAction'), 'updateAction must exist');
		$this->assertTrue(method_exists($controller, 'deleteAction'), 'deleteAction must exist');
	}


	/**
	 * @test
	 */
	public function handleRequest_allMandatoryActions_areResolvedToMethodNames()
	{
		$request = HttpRequest::create();

		$request->setAttachedVar('action', 'list');
		$controller = $this->makeInitiatedController();
		$controller->chooseAction($request);
		$this->assertEquals('listAction', $controller->guessMethodNameByAction($controller->getCurrentAction()));


		$request->setAttachedVar('action', 'view');
		$controller = $this->makeInitiatedController();
		$controller->chooseAction($request);
		$this->assertEquals('viewAction', $controller->guessMethodNameByAction($controller->getCurrentAction()));



		$request->setAttachedVar('action', 'create');
		$controller = $this->makeInitiatedController();
		$controller->chooseAction($request);
		$this->assertEquals('createAction', $controller->guessMethodNameByAction($controller->getCurrentAction()));


		$request->setAttachedVar('action', 'update');
		$controller = $this->makeInitiatedController();
		$controller->chooseAction($request);
		$this->assertEquals('updateAction', $controller->guessMethodNameByAction($controller->getCurrentAction()));


		$request->setAttachedVar('action', 'delete');
		$controller = $this->makeInitiatedController();
		$controller->chooseAction($request);
		$this->assertEquals('deleteAction', $controller->guessMethodNameByAction($controller->getCurrentAction()));
	}


	/**
	 * @test
	 */
	public function handleRequest_entityNameCanBeResolved_resolvesEntityName()
	{
		$request = HttpRequest::create();

		//Resolve from class constant
		$controller = $this->makeInitiatedController();
		$controller->handleRequest($request);
		$this->assertEquals('TestEntity', $controller->getEntityName());

		//Resolve from attached var
		$controller = new TestEntityNoEntityConstCRUDController();
		$request->setAttachedVar('entityName', 'TestEntity');
		$controller->handleRequest($request);
		$this->assertEquals('TestEntity', $controller->getEntityName());

		//Resolve from controller class name
		$request = HttpRequest::create();
		$controller = new TestEntityNoEntityConstCRUDController();
		$controller->handleRequest($request);
		$this->assertEquals('TestEntityNoEntityConst', $controller->getEntityName());
	}


	/**
	 * @test
	 */
	public function handleRequest_listAction_returnsMaVWithArrayOfEntityObjectsOrEmptyArray()
	{
		$controller = $this->makeInitiatedController();
		$request = HttpRequest::create();

		$mav = $controller->handleRequest($request);
		$list = $mav->getModel()->get('entityList');

		$this->assertTrue(is_array($list));
	}


	/**
	 * @test
	 */
	public function handleRequest_listActionWithSearchFilter_criteriaHasSearchFilterApplied()
	{
		$controller = $this->makeFakeInitiatedController(array('getEntityList'));
		$request = HttpRequest::create();
		$searchFilter = array(
			'name' => 'test',
			'parent.name' => 'parent'
		);
		$request->setGetVar('search', $searchFilter);

		$controller->
			expects($this->once())->
			method('getEntityList')->
			will($this->returnCallback(
				function (Criteria $criteria) {
					PHPUnit_Framework_Assert::assertEquals(/*search filter size*/ 2, $criteria->getLogic()->getSize());
				}
			))
		;

		$controller->handleRequest($request);
	}


	/**
	 * @test
	 */
	public function handleRequest_editActionNoEntityId_returnsMaVWithCreationFormAndFormHelperAndEditView()
	{
		$controller = $this->makeInitiatedController();
		$request = HttpRequest::create();
		$request->setGetVar('action', 'edit');

		$mav = $controller->handleRequest($request);
		$model = $mav->getModel();
		$form = $model->get('form');

		$this->assertEquals('editAction', $mav->getView());

		$this->assertInstanceOf('Form', $form);
		$this->assertInstanceOf('FormHelper', $model->get('formHelper'));

		$this->assertFalse($form->exists('id'));
		$this->assertEmpty($form->getValue('name'));
		$this->assertEmpty($form->getValue('isActive'));
	}


	/**
	 * @test
	 */
	public function handleRequest_editActionValidEntityIdInRequest_returnsMaVWithUpdateFormAndFormHelperAndEditView()
	{
		$controller = $this->makeInitiatedController();
		$request = HttpRequest::create();
		$request->setGetVar('action', 'edit');
		$request->setGetVar('id', TestEntity::FAKE_ID_FOR_TEST);

		$mav = $controller->handleRequest($request);
		$model = $mav->getModel();
		$form = $model->get('form');

		$this->assertEquals('editAction', $mav->getView());

		$this->assertInstanceOf('Form', $form);
		$this->assertInstanceOf('FormHelper', $model->get('formHelper'));

		$this->assertInstanceOf('TestEntity', $form->getValue('id'));
		$this->assertNotEmpty($form->getValue('name'));
		$this->assertInstanceOf('TimestampTZ', $form->getValue('createdAt'));
		$this->assertTrue($form->getValue('isActive'));
	}


	/**
	 * @test
	 */
	public function handleRequest_createActionInvalidDataInRequest_returnsMaVWithFormAndErrorsAndEditActionView()
	{
		$controller = $this->makeInitiatedController();
		$request = HttpRequest::create();
		$post = array(
			'action' => 'create',
			'DATA' => array(
				'name' => '',
				'isActive' => 1
			)
		);
		$request->setPost($post);

		$mav = $controller->handleRequest($request);
		$model = $mav->getModel();
		$view = $mav->getView();
		$form = $model->get('form');

		$this->assertEquals('editAction', $mav->getView());
		$this->assertCount(1, $form->getErrors());
	}


	/**
	 * @test
	 */
	public function handleRequest_createActionValidDataInRequest_returnsMaVWithRedirectViewAndSetsFlashMessage()
	{
		$controller = $this->makeInitiatedController();
		$mockFlashMessageManager = $this->makeFakeFlashMessageManager(array('addMessage'));
		$request = HttpRequest::create();
		$post = array(
			'action' => 'create',
			'DATA' => array(
				'name' => 'Тестовая сущность',
				'isActive' => 1
			)
		);

		$mockFlashMessageManager->
			expects($this->once())->
			method('addMessage')->
			with($this->equalTo('crudMessage'), $this->stringContains('created'))->
			will($this->returnSelf())
		;
		$controller->setFlashMessagesManager($mockFlashMessageManager);
		$request->setPost($post);

		$mav = $controller->handleRequest($request);
		$view = $mav->getView();
		$urlToRedirectTo =
			RouterUrlHelper::url(
				array(
					SYSTEM_CONTROLLER_VAR_NAME => 'test-entity',
					'action' => 'list'
				), 'entity'
			);

		$this->assertInstanceOf('CleanRedirectView', $view);
		$this->assertEquals($urlToRedirectTo, $view->getUrl());
	}


	/**
	 * @test
	 */
	public function handleRequest_updateActionInvalidDataInRequest_returnsMaVWithFormAndErrorsAndEditActionView()
	{
		$controller = $this->makeInitiatedController();
		$request = HttpRequest::create();
		$post = array(
			'action' => 'update',
			'DATA' => array(
				'name' => '',
				'isActive' => 1
			)
		);
		$request->setPost($post);

		$mav = $controller->handleRequest($request);
		$model = $mav->getModel();
		$view = $mav->getView();
		$form = $model->get('form');

		$this->assertEquals('editAction', $mav->getView());
		$this->assertCount(2, $form->getErrors());
	}


	/**
	 * @test
	 */
	public function handleRequest_updateActionValidDataInRequest_returnsMaVWithRedirectViewAndSetsFlashMessage()
	{
		$controller = $this->makeInitiatedController();
		$mockFlashMessageManager = $this->makeFakeFlashMessageManager(array('addMessage'));
		$request = HttpRequest::create();
		$post = array(
			'action' => 'update',
			'DATA' => array(
				'id' => TestEntity::FAKE_ID_FOR_TEST,
				'name' => 'Тестовая сущность',
				'isActive' => 1
			)
		);

		$mockFlashMessageManager->
			expects($this->once())->
			method('addMessage')->
			with($this->equalTo('crudMessage'), $this->stringContains('updated'))->
			will($this->returnSelf())
		;
		$controller->setFlashMessagesManager($mockFlashMessageManager);
		$request->setPost($post);

		$mav = $controller->handleRequest($request);
		$view = $mav->getView();
		$urlToRedirectTo =
			RouterUrlHelper::url(
				array(
					SYSTEM_CONTROLLER_VAR_NAME => 'test-entity',
					'action' => 'list'
				), 'entity'
			);

		$this->assertInstanceOf('CleanRedirectView', $view);
		$this->assertEquals($urlToRedirectTo, $view->getUrl());
	}


	/**
	 * @test
	 */
	public function handleRequest_deleteActionAnyDataInRequest_attemptsToDeleteRecordAndReturnsMaVWithRedirectViewAndSetsFlashMessage()
	{
		$controller = $this->makeInitiatedController();
		$mockFlashMessageManager = $this->makeFakeFlashMessageManager(array('addMessage'));
		$request = HttpRequest::create();
		$post = array(
			'action' => 'delete',
			'DATA' => array(
				'id' => TestEntity::FAKE_ID_FOR_TEST
			)
		);

		$mockFlashMessageManager->
			expects($this->once())->
			method('addMessage')->
			with($this->equalTo('crudMessage'), $this->stringContains('deleted'))->
			will($this->returnSelf())
		;
		$controller->setFlashMessagesManager($mockFlashMessageManager);
		$request->setPost($post);

		$mav = $controller->handleRequest($request);
		$view = $mav->getView();
		$urlToRedirectTo =
			RouterUrlHelper::url(
				array(
					SYSTEM_CONTROLLER_VAR_NAME => 'test-entity',
					'action' => 'list'
				), 'entity'
			);

		$this->expectOutputString('deleted'); //fake entity dao output
		$this->assertInstanceOf('CleanRedirectView', $view);
		$this->assertEquals($urlToRedirectTo, $view->getUrl());
	}


	/**************************************************************
	 **************** TEST CREATION METHODS & UTILS ***************
	 **************************************************************/
	private function makeInitiatedController()
	{
		$controller = new TestEntityCRUDController();

		$controller->setLoggerWrapper(Logger::create());

		return $controller;
	}

	private function makeFakeInitiatedController($fakeMethods = array())
	{
		$fakeController = $this->getMock('TestEntityCRUDController', $fakeMethods);

		return $fakeController;
	}

	/**
	 * @return PHPUnit_Framework_MockObject_MockObject
	 */
	protected function makeFakeFlashMessageManager(array $methods = array())
	{
		return $this->getMock('FlashMessageManager', $methods);
	}
}


class TestEntityCRUDController extends BaseCRUDController {
	const ENTITY_NAME = 'TestEntity';

	protected function getEntityList(Criteria $criteria)
	{
		return array(TestEntity::create(), TestEntity::create());
	}

	protected function makePager(HttpRequest $request, Criteria $criteria, array $filterData)
	{
		return PagerUtils::makePager();
	}


}

class TestEntity extends IdentifiableObject implements DAOConnected{

	const FAKE_ID_FOR_TEST = 5;

	protected $name;
	protected $createdAt;
	protected $isActive = false;


	public static function create()
	{
		return new static;
	}

	public static function dao()
	{
		return Singleton::getInstance('TestEntityDAO');
	}

	public static function proto()
	{
		return Singleton::getInstance('TestEntityProto');
	}


	/**
	 * @return mixed
	 */
	public function getCreatedAt()
	{
		return $this->createdAt;
	}

	/**
	 * @return TestEntity
	 */
	public function setCreatedAt($createdAt)
	{
		$this->createdAt = $createdAt;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getIsActive()
	{
		return $this->isActive;
	}

	/**
	 * @return TestEntity
	 */
	public function setIsActive($isActive)
	{
		$this->isActive = $isActive;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @return TestEntity
	 */
	public function setName($name)
	{
		$this->name = $name;
		return $this;
	}
}

class TestEntityDAO extends StorableDAO {

	public function getById($id, $expires = Cache::EXPIRES_MEDIUM)
	{
		$object =
			TestEntity::create()->
				setName('Test name with немного кириллицы!')->
				setCreatedAt(TimestampTZ::makeNow())->
				setIsActive(true)->
				setId(TestEntity::FAKE_ID_FOR_TEST)
		;

		return $object;
	}

	public function dropById($id)
	{
		echo 'deleted';

		return 1;
	}


	public function take(Identifiable $object)
	{
		if(!$object->getId()) {
			$object->setId(TestEntity::FAKE_ID_FOR_TEST);
		}

		return $object;
	}

	public function save(Identifiable $object)
	{
		return $object;
	}


	public function getTable()
	{
		return 'test_entity';
	}

	public function getObjectName()
	{
		return 'TestEntity';
	}

	public function getSequence()
	{
		return 'test_entity_id';
	}
}

class TestEntityProto extends AbstractProtoClass {

	public function makeFormCreate($prefix = null)
	{
		$form =  parent::makeForm($prefix);
		$form->drop('id');
		$form->drop('createdAt');

		return $form;
	}

	public function makeFormUpdate($prefix = null)
	{
		$form =  parent::makeForm($prefix);
		return $form;
	}


	protected function makePropertyList()
	{
		return array(
			'id' => LightMetaProperty::fill(new LightMetaProperty(), 'id', null, 'integerIdentifier', 'TestEntity', 4, true, true, false, null, null),
			'name' => LightMetaProperty::fill(new LightMetaProperty(), 'name', null, 'string', null, 255, true, true, false, null, null),
			'isActive' => LightMetaProperty::fill(new LightMetaProperty(), 'isActive', 'is_active', 'boolean', null, null, false, true, false, null, null),
			'createdAt' => LightMetaProperty::fill(new LightMetaProperty(), 'createdAt', 'created_at', 'timestampTZ', 'TimestampTZ', null, false, true, false, null, null)
		);
	}
}


class TestEntityNoEntityConstCRUDController extends BaseCRUDController {
	protected function listAction(HttpRequest $request)
	{
		return $this->mav;
	}
}


class TestEntityNoEntityConst extends IdentifiableObject {}
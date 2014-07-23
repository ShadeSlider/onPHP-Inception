<?php

/**
 * @author Eric I. Gorbikov <ernest.gorbikov@gmail.com>
 * @copyright 2014 Eric I. Gorbikov <ernest.gorbikov@gmail.com>
 */

class BaseControllerTestCase extends BaseTestCase {


	/**
	 * @test
	 */
	public function handleRequest_always_returnsModelAndView()
	{
		$controller = $this->makeFakeControllerWithActions();

		$mav = $controller->handleRequest(HttpRequest::create());

		$this->assertInstanceOf('ModelAndView', $mav);
	}


	/**
	 * @test
	 */
	public function chooseAction_noActionInRequest_returnsDefaultActionUsingNameConvention()
	{
		$mockController = $this->makeFakeControllerWithActions();
		$request = HttpRequest::createFromGlobals();

		$chosenAction = $mockController->chooseAction($request);

		$this->assertContains('default', $chosenAction);
	}


	/**
	 * @test
	 */
	public function chooseAction_actionInRequest_returnsActionUsingNameConvention()
	{
		$mockController = $this->makeFakeControllerWithActions();
		$request = HttpRequest::createFromGlobals();
		$request->setAttachedVar('action', 'test');

		$chosenAction = $mockController->chooseAction($request);

		$this->assertContains('test', $chosenAction);
	}


	/**
	 * @test
	 */
	public function handleRequest_always_callsBeforeActionMethod()
	{
		$mockController = $this->makeMockableFakeControllerWithActions(array('beforeAction'));

		$mockController->
			expects($this->once())->
			method('beforeAction')->
			with($this->isInstanceOf('HttpRequest'))
		;

		$mockController->handleRequest(HttpRequest::create());
	}


	/**
	 * @test
	 */
	public function handleRequest_beforeActionReturnsTrue_callsAfterActionMethod()
	{
		$mockController = $this->makeMockableFakeControllerWithActions(array('beforeAction', 'afterAction',));

		$mockController->
			expects($this->once())->
			method('beforeAction')->
			with($this->isInstanceOf('HttpRequest'))->
			will($this->returnValue(true))
		;

		$mockController->
			expects($this->once())->
			method('afterAction')->
			with($this->isInstanceOf('HttpRequest'))
		;

		$mockController->handleRequest(HttpRequest::create());
	}


	/**
	 * @test
	 */
	public function handleRequest_beforeActionReturnsFalse_doesNotCallAfterActionMethod()
	{
		$mockController = $this->makeMockableFakeControllerWithActions(array('beforeAction', 'afterAction',));

		$mockController->
			expects($this->once())->
			method('beforeAction')->
			with($this->isInstanceOf('HttpRequest'))->
			will($this->returnValue(false))
		;

		$mockController->
			expects($this->never())->
			method('afterAction')->
			with($this->isInstanceOf('HttpRequest'))
		;

		$mockController->handleRequest(HttpRequest::create());
	}


	/**
	 * @test
	 */
	public function getCleanName_always_returnsNameWithoutController()
	{
		$controller = $this->makeInitiatedController();

		$cleanControllerName = $controller->getCleanName();

		$this->assertEquals('FakeBase', $cleanControllerName);
	}


	/**
	 * @test
	 * @expectedException WrongStateException
	 */
	public function log_loggerWrapperNotSet_throwsWrongStateException()
	{
		$controller = $this->makeInitiatedController();
		$controller->setLoggerWrapper(null);

		$controller->log('test');
	}


	/**
	 * @test
	 */
	public function log_logMessageIsAString_callsLoggerWrapperLogMethod()
	{
		$controller = $this->makeInitiatedController();
		$fakeLoggerWrapper = $this->makeMockableFakeLoggerWrapper(array('log'));
		$controller->setLoggerWrapper($fakeLoggerWrapper);


		$fakeLoggerWrapper->
			expects($this->once())->
			method('log')->
			with($this->isInstanceOf('LogLevel'), $this->stringContains('test'))
		;

		$controller->log('test');
	}


	/**
	 * @test
	 */
	public function log_logMessageIsALogRecord_callsLoggerWrapperLogRecordMethod()
	{
		$controller = $this->makeInitiatedController();
		$fakeLoggerWrapper = $this->makeMockableFakeLoggerWrapper(array('logRecord'));
		$controller->setLoggerWrapper($fakeLoggerWrapper);


		$fakeLoggerWrapper->
			expects($this->once())->
			method('logRecord')->
			with($this->isInstanceOf('LogRecord'))
		;

		$logRecord =
			LogRecord::create()->
			setMessage('test')->
			setDate(Timestamp::makeNow())->
			setLevel(LogLevel::finest())
		;

		$controller->log($logRecord);
	}

	/**************************************************************
	 **************** TEST CREATION METHODS & UTILS ***************
	 **************************************************************/
	private function makeInitiatedController()
	{
		$controller = new FakeBaseController();

		$controller->setLoggerWrapper(Logger::create());

		return $controller;
	}

	private function makeMockableFakeLoggerWrapper($fakeMethods = array())
	{
		$fakeLoggerWrapper = $this->getMock('Logger', $fakeMethods);

		return $fakeLoggerWrapper;
	}

	private function makeMockableFakeController($fakeMethods = array())
	{
		$fakeController = $this->getMock('FakeBaseController', $fakeMethods);

		return $fakeController;
	}

	private function makeMockableFakeControllerWithActions($fakeMethods = array())
	{
		$fakeController = $this->getMock('FakeBaseControllerWithActions', $fakeMethods);

		return $fakeController;
	}

	private function makeFakeControllerWithActions()
	{
		return new FakeBaseControllerWithActions();
	}
}


class FakeBaseController extends BaseController {}

class FakeBaseControllerWithActions extends BaseController {

	public function defaultAction(HttpRequest $request)
	{
		return ModelAndView::create();
	}

	public function testAction(HttpRequest $request)
	{
		return ModelAndView::create();
	}
}

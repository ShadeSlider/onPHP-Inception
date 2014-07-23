<?php
/**
 * @author Eric I. Gorbikov <ernest.gorbikov@gmail.com>
 * @copyright 2014
 */

class WebApplicationTestCase extends PHPUnit_Framework_TestCase {


	/**
	 * @test
	 */
	public function resolveController_noOrNonexistentControllerVarSetInRequest_returnNotFoundController()
	{

		$webApplication = $this->makeNewInitiatedWebApplicationInstance();
		$config = $this->makeNewConfig();
		$request = $this->makeNewRequest();

		//No controller var is set
		$controller = $webApplication->resolveController($request);
		$this->assertEquals(get_class($controller), $config->getSetting(WebApplicationConfig::NOT_FOUND_CONTROLLER_VAR));

		//Nonexistent controller var is set
		$request->setGetVar(SYSTEM_CONTROLLER_VAR_NAME, 'nonexistent');
		$controller = $webApplication->resolveController($request);
		$this->assertEquals(get_class($controller), $config->getSetting(WebApplicationConfig::NOT_FOUND_CONTROLLER_VAR));
	}


	/**
	 * @test
	 */
	public function generateModelAndView_mainControllerNotSet_returnModelAndView()
	{
		$webApplication = $this->makeNewInitiatedWebApplicationInstance();

		$mav = $webApplication->generateModelAndView(HttpRequest::create());

		$this->assertInstanceOf('ModelAndView', $mav);
	}


	/**
	 * @test
	 */
	public function generateModelAndView_always_ModelAndViewContainsControllerAndAction()
	{
		$webApplication = $this->makeNewInitiatedWebApplicationInstance();

		$mav = $webApplication->generateModelAndView(HttpRequest::create());
		$model = $mav->getModel();

		$this->assertEquals('notfound', $model->get('controllerName'));
		$this->assertEquals('NotFound', $model->get('controllerNameClean'));
		$this->assertEquals('not-found', $model->get('controllerNameDashed'));
		$this->assertEquals('default', $model->get('controllerActionName'));
	}


	/**
	 * @test
	 * @expectedException WrongStateException
	 */
	public function processRequest_notIsInitiated_throwsWrongStateException()
	{
		$mockWebApplication = $this->getMock('WebApplication', array('isInitiated'));

		$mockWebApplication->
			expects($this->once())->
			method('isInitiated')->
			will($this->returnValue(false))
		;

		$mockWebApplication->processRequest(HttpRequest::create());
	}


	/**
	 * @test
	 */
	public function processRequest_viewIsEmpty_outputsEmptyString()
	{
		$mockWebApplication = $this->makeNewInitiatedFakeWebApplicationInstance(array('generateModelAndView'));
		$mavWithEmptyView =
			ModelAndView::create()->
			setView('')
		;

		$mockWebApplication->
			expects($this->once())->
			method('generateModelAndView')->
			with($this->isInstanceOf('HttpRequest'))->
			will($this->returnValue($mavWithEmptyView))
		;

		$this->expectOutputString('');

		$mockWebApplication->processRequest(HttpRequest::create());
	}


	/**
	 * @test
	 */
	public function processRequest_viewIsAnExistingSimplePhpView_rendersView()
	{
		$mockWebApplication = $this->makeNewInitiatedFakeWebApplicationInstance(array('generateModelAndView'));
		$mavWithSimplePhpView =
			ModelAndView::create()->
				setView($this->makeFakeSimplePHPView())
		;

		$mockWebApplication->
			expects($this->once())->
			method('generateModelAndView')->
			will($this->returnValue($mavWithSimplePhpView))
		;

		$this->expectOutputRegex('/SimplePhpView/i');
		$mockWebApplication->processRequest(HttpRequest::create());
	}


	/**
	 * @test
	 */
	public function processRequest_viewIsRedirectMatchingARoute_redirectsToARoute()
	{
		$mockWebApplication = $this->makeNewInitiatedFakeWebApplicationInstance(array('generateModelAndView'));
		RouterRewrite::me()->
			addRoute('existing_route', RouterStaticRule::create('/existing_route_url'))
		;
		$mavWithRedirectView =
			ModelAndView::create()->
			setView($this->makeFakeRedirectView('existing_route'))
		;

		$mockWebApplication->
			expects($this->once())->
			method('generateModelAndView')->
			will($this->returnValue($mavWithRedirectView))
		;

		$mockWebApplication->processRequest(HttpRequest::create());

		$this->expectOutputRegex('/RedirectView/i');

		RouterRewrite::me()->removeRoute('existing_route');
	}


	/**
	 * @test
	 * @expectedException RouterException
	 */
	public function processRequest_viewIsRedirectNotMatchingARoute_throwsRouterException()
	{
		$mockWebApplication = $this->makeNewInitiatedFakeWebApplicationInstance(array('generateModelAndView'));
		RouterRewrite::me()->
			addRoute('existing_route', RouterStaticRule::create('/'))
		;
		$mavWithRedirectView =
			ModelAndView::create()->
			setView($this->makeFakeRedirectView('nonexistent_route'))
		;

		$mockWebApplication->
			expects($this->once())->
			method('generateModelAndView')->
			will($this->returnValue($mavWithRedirectView))
		;


		$mockWebApplication->processRequest(HttpRequest::create());
		RouterRewrite::me()->removeRoute('existing_route');
	}


	/**
	 * @test
	 */
	public function processRequest_viewIsAnExisitingViewName_searchesDirectoriesForATemplateInProperOrder()
	{
		$stubWebApplication = $this->makeNewInitiatedFakeWebApplicationInstance(array('generateModelAndView'));
		$mockViewResolver = $this->makeFakeVerbosePhpViewResolver();


		$mavWithStringView =
			ModelAndView::create()->
				setView('existing_view')
		;
		$stubWebApplication->
			expects($this->once())->
			method('generateModelAndView')->
			will($this->returnValue($mavWithStringView))
		;


		$controllerPrefixForTest = 'fake';
		$controllerFullNameForTest = $controllerPrefixForTest . 'Controller';
		$regexFriendlyTemplatesDirName = preg_quote(DIR_TEMPLATES . 'controllers' . DS, '/');
		$regex = '/'.$regexFriendlyTemplatesDirName . $controllerPrefixForTest . preg_quote(DS, '/') . '(.*)'.$regexFriendlyTemplatesDirName.'/i';

		$stubWebApplication->setMainController(new $controllerFullNameForTest);
		$stubWebApplication->setViewResolver($mockViewResolver);


		$this->expectOutputRegex($regex);
		$stubWebApplication->processRequest(HttpRequest::create());
	}


	/**
	 * @test
	 */
	public function processRequest_viewIsAnExisitingViewName_resolvesAndRendersView()
	{
		$mockWebApplication = $this->makeNewInitiatedFakeWebApplicationInstance(array('generateModelAndView'));
		$mavWithStringView =
			ModelAndView::create()->
				setView('existing_view')
		;
		$mockWebApplication->
			expects($this->once())->
			method('generateModelAndView')->
			will($this->returnValue($mavWithStringView))
		;

		$this->expectOutputRegex('/SimplePhpView/i');
		$mockWebApplication->processRequest(HttpRequest::create());
	}


	/**
	 * @test
	 * @expectedException WrongArgumentException
	 */
	public function processRequest_viewIsANonexistentViewName_throwsWrongArgumentException()
	{
		$mockWebApplication = $this->makeNewInitiatedFakeWebApplicationInstance(array('generateModelAndView'));
		$mockWebApplication->setViewResolver(MultiPrefixPhpViewResolver::create()->addFirstPrefix(DIR_TEMPLATES));
		$mavWithStringView =
			ModelAndView::create()->
				setView('nonexistent_view')
		;
		$mockWebApplication->
			expects($this->once())->
			method('generateModelAndView')->
			will($this->returnValue($mavWithStringView))
		;

		$mockWebApplication->processRequest(HttpRequest::create());
	}


	/**************************************************************
	 **************** TEST CREATION METHODS & UTILS ***************
	 *************************************************************
	 * @param null $config
	 * @param null $viewResolver
	 * @param null $loggerWrapper
	 * @throws MissingElementException
	 * @return \WebApplication
	 */
	private function makeNewWebApplicationInstance($config = null, $viewResolver = null, $loggerWrapper = null)
	{
		WebApplication::me();
		WebApplication::dropInstance('WebApplication');

		$webApplication = WebApplication::me();

		if($config instanceof Config) {
			$webApplication->setConfig($config);
		}
		if($viewResolver instanceof ViewResolver) {
			$webApplication->setViewResolver($viewResolver);
		}
		if($loggerWrapper instanceof Logger) {
			$webApplication->setLoggerWrapper($loggerWrapper);
		}

		return $webApplication;
	}

	private function makeNewInitiatedWebApplicationInstance()
	{
		$config = $this->makeNewConfig();
		$stubViewResolver = $this->makeFakePhpViewResolver();
		$loggerWrapper = $this->makeFakeLoggerWrapper();

		return $this->makeNewWebApplicationInstance($config, $stubViewResolver, $loggerWrapper);
	}

	private function makeNewInitiatedFakeWebApplicationInstance($fakeMethods = array())
	{
		$config = $this->makeNewConfig();
		$request = $this->makeNewRequest();
		$stubViewResolver = $this->makeFakePhpViewResolver();
		$loggerWrapper = $this->makeFakeLoggerWrapper();

		$fakeWebApplication = $this->getMock('WebApplication', $fakeMethods);
		$fakeWebApplication->
			setConfig($config)->
			setViewResolver($stubViewResolver)->
			setLoggerWrapper($loggerWrapper)
		;

		return $fakeWebApplication;
	}


	private function makeNewConfig()
	{
		return WebApplicationConfig::create();
	}

	private function makeNewRequest()
	{
		return HttpRequest::create();
	}

	private function makeFakeLoggerWrapper()
	{
		$loggerWrapper = Logger::create()->setLevel(LogLevel::finest());
		$loggerWrapper->flushLoggers();

		$loggerWrapper->add(FileLogger::create(SYSTEM_TMP_DIR));

		return $loggerWrapper;
	}



	private function makeFakeSimplePHPView($url = '/')
	{
		return new FakeSimplePhpView($url, $this->makeFakePhpViewResolver());
	}

	private function makeFakeRedirectView($url = '/')
	{
		return new FakeRedirectView($url);
	}

	private function makeFakePhpViewResolver()
	{
		return new FakeMultiPrefixPhpViewResolver();
	}
	private function makeFakeVerbosePhpViewResolver()
	{
		return new FakeVerboseMultiPrefixPhpViewResolver();
	}
}

/**************************************************************
 ******************** FAKE CLASSES FOR TESTS ******************
 **************************************************************/

class FakeController implements Controller {

	public function handleRequest(HttpRequest $request)
	{
		return ModelAndView::create();
	}
}

class FakeSimplePhpView extends SimplePhpView {

	public function render($model = null)
	{
		echo 'SimplePhpView is rendered';
	}

}
class FakeRedirectView extends RedirectView {

	public function render($model = null)
	{
		echo 'RedirectView is rendered (redirect has been performed)';
	}

}


class FakeMultiPrefixPhpViewResolver extends MultiPrefixPhpViewResolver {

	public function resolveViewName($viewName)
	{
		return new FakeSimplePhpView('/', $this);
	}
}

class FakeVerboseMultiPrefixPhpViewResolver extends MultiPrefixPhpViewResolver {

	public function resolveViewName($viewName)
	{
		return new FakeSimplePhpView('/', $this);
	}

	public function addPrefix($prefix, $alias = null)
	{
		echo $prefix . '\n';
		return parent::addPrefix($prefix, $alias);
	}
}

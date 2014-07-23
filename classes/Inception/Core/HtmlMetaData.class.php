<?php
/**
 * @author Eric I. Gorbikov <ernest.gorbikov@gmail.com>
 * @copyright 2014 Eric I. Gorbikov <ernest.gorbikov@gmail.com>
 */

class HtmlMetaData {

	/** @var HttpRequest  */
	protected $request;

	/** @var Controller  */
	protected $controller;

	protected $titlePrefix = '';

	public static function create(HttpRequest $request, Controller $controller = null)
	{
		return new static($request, $controller);
	}

	public function __construct(HttpRequest $request, Controller $controller = null)
	{
		$this->request = $request;
		$this->controller = $controller;
	}

	public function getTitle()
	{
		$title = 'no-title-set';
		$controller = $this->controller;
		$controllerClass = get_class($controller);

		while(true) {

			if(method_exists($this->controller, 'getMetaDataManager')) {
				/** @var BaseController $controller */
				$metaData = $controller->getMetaDataManager()->getControllerMetaData($controller->getCleanName());

				if(isset($metaData['htmlTitle'])) {
					$title = $metaData['htmlTitle'];
					break;
				}

				if(isset($metaData['title'])) {
					$title = $metaData['title'];
					break;
				}
			}

			try {
				$title = constant("$controllerClass::TITLE");
				break;
			}
			catch (BaseException $e) {
				/* constant TITLE is not defined */
			}


			$title = $controllerClass;
			if(method_exists($controller, 'getCurrentAction')) {
				$actionName = $controller->getCurrentAction();
				$title .= ' - ' . $actionName;
			}
			break;
		}


		return
			$this->getTitlePrefix()
				? $this->getTitlePrefix() . ' - ' . $title
				: $title
			;
	}


	public function getKeywords()
	{
		return '';
	}


	public function getDescription()
	{
		return '';
	}

	/**************************************************************
	 ********************* GETTERS & SETTERS **********************
	 **************************************************************/
	/**
	 * @return string
	 */
	public function getTitlePrefix()
	{
		return $this->titlePrefix;
	}

	/**
	 * @return HtmlMetaData
	 */
	public function setTitlePrefix($titlePrefix)
	{
		$this->titlePrefix = $titlePrefix;
		return $this;
	}
}
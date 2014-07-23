<?php
/**
 * @author Eric I. Gorbikov <ernest.gorbikov@gmail.com>
 * @copyright 2014 Eric I. Gorbikov <ernest.gorbikov@gmail.com>
 */

class ExtendedPartViewer extends ExtendedPhpView {

	/** @var Model */
	protected $model = null;

	/**
	 * @param ViewResolver $resolver
	 * @param null $model
	 */
	public function __construct(ViewResolver $resolver, $model = null, $metaData = array())
	{
		$this->partViewResolver = $resolver;
		$this->model = $model;
		$this->metaData = $metaData;
	}


	/**
	 * @return static
	 **/
	public function view($partName, $model = null, $mergeModel = true)
	{
		if(is_array($model)) {
			$modelNew = Model::create();

			foreach ($model as $name => $value) {
				$modelNew->set($name, $value);
			}
			$model = $modelNew;

		}

		if($mergeModel && $model instanceof Model) {
			$model = $model->merge($this->getModel());
		}

		Assert::isTrue($model === null || $model instanceof Model);

		// use model from outer template if none specified
		if ($model === null) {
			$model = $this->model;

			$parentModel = $this->model->has('parentModel')
				? $this->model->get('parentModel')
				: null;

		} else
			$parentModel = $this->model;

		$model->set('parentModel', $parentModel);

		$rootModel = $this->model->has('rootModel')
			? $this->model->get('rootModel')
			: $this->model;

		$model->set('rootModel', $rootModel);

		if ($partName instanceof View)
			$partName->render($model);
		else {
			$view = $this->partViewResolver->resolveViewName($partName);
			if($view instanceof ExtendedPhpView) {
				$view->setMetaData($this->getMetaData());
			}
			$view->render($model);
		}


		return $this;
	}


	/**
	 * @return static
	 */
	public function viewPartOrDefault($partName, $defaultPartName, $model = null, $mergeModel = true)
	{
		if(!$this->partExists($partName)) {
			$partName = $defaultPartName;
		}

		return $this->view($partName, $model, $mergeModel);
	}


	/**
	 * @return string|static
	 */
	public function viewPartIfExists($partName, $model = null, $mergeModel = true)
	{
		if(!$this->partExists($partName)) {
			return '';
		}

		return $this->view($partName, $model, $mergeModel);
	}


	/**
	 * @return string
	 * @throws BaseException
	 */
	public function toString($model = null, $partName = null)
	{
		try {
			ob_start();
			$this->view($partName, $model);
			return ob_get_clean();
		} catch (BaseException $e) {
			ob_end_clean();
			throw $e;
		}
	}


	/**
	 * @return bool
	 */
	public function partExists($partName)
	{
		return $this->partViewResolver->viewExists($partName);
	}


	/**
	 * @return Model
	 **/
	public function getModel()
	{
		return $this->model;
	}
}
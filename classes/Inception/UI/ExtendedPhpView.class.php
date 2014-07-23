<?php
/**
 * @author Eric I. Gorbikov <ernest.gorbikov@gmail.com>
 * @copyright 2014 Eric I. Gorbikov <ernest.gorbikov@gmail.com>
 */

class ExtendedPhpView extends SimplePhpView {

	/** @var array */
	protected $metaData = array();

	const ENTITY_PROPERTY_LIST_TEMPLATE = 'property.list';


	/**
	 * @return static
	 **/
	public function render(/* Model */ $model = null)
	{
		if(is_array($model)) {
			$modelNew = Model::create();

			foreach ($model as $name => $value) {
				$modelNew->set($name, $value);
			}
			$model = $modelNew;

		}

		Assert::isTrue($model === null || $model instanceof Model);

		if ($model)
			extract($model->getList());

		$view = $this;

		$partViewer = new ExtendedPartViewer($this->partViewResolver, $model, $this->metaData);

		$this->preRender();

		include $this->templatePath;

		$this->postRender();

		return $this;
	}


	/**
	 * @return array
	 */
	public function getMetaData()
	{
		return $this->metaData;
	}


	/**
	 * @return static
	 */
	public function setMetaData($metaData)
	{
		$this->metaData = $metaData;

		return $this;
	}


	/**
	 * @return array
	 */
	public function getColumnList($suffix = '')
	{
		$listColumns = array();

		if(isset($this->metaData['listColumns' . $suffix])) {
			$listColumns = $this->metaData['listColumns' . $suffix];
		}

		return $listColumns;
	}


	/**
	 * @return string
	 */
	public function displayEntityProperty($propertyValue, $propertyTemplate = null)
	{
		if(is_array($propertyValue)) {
			$propertyTemplate = $propertyTemplate ?: 'tiny' . DS . 'entity' . DS . static::ENTITY_PROPERTY_LIST_TEMPLATE;

			return $this->partViewResolver->resolveViewName($propertyTemplate)->toString(Model::create()->set('value', $propertyValue));
		}
		else {
			return $propertyValue;
		}
	}
}
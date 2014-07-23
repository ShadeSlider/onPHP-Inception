<?php
/**
 * @author Eric I. Gorbikov <ernest.gorbikov@gmail.com>
 * @copyright 2009-2014 Eric I. Gorbikov <ernest.gorbikov@gmail.com>
 */
class FormHelper {

	/**
	 * @var Form
	 */
	protected $form = null;
	protected $scopeName = null;
	protected $fieldsWithErrors;

	protected function __construct($form = null, $scopeName = null, $fieldsWithErrors = true)
	{
		$this->fieldsWithErrors = $fieldsWithErrors;
		if($form instanceof Form) {
			$this->form = $form;
		}
		else {
			$this->form = Form::create();
		}
		$this->scopeName = $scopeName;

		$this->viewResolver = TemplateUtils::makeDefaultViewResolver();
	}

	/**
	 * @return FormHelper
	**/
	public static function create($form = null, $scopeName = null, $fieldsWithErrors = true)
	{
		return new self($form, $scopeName);
	}


	private function getFieldValue($prmName, $extraParams, $extraSettings)
	{
		$valueType = '';
		if(array_key_exists('valueType', $extraSettings)) {
			$valueType = $extraSettings['valueType'];
		}
		
		$valueMethodName = 'get'.$valueType.'Value';
		if(!$this->form->exists($prmName)) {
			return '';
		}
		$prm = $this->form->get($prmName);
		$prmValue = $prm->$valueMethodName();

		if(is_object($prmValue)) {
			switch(get_class($prmValue)) {
				case 'Date':
					if(defined('DATE_FORMAT')) {
						$prmValue = $prmValue->toFormatString(DATE_FORMAT);
					}
					else {
						$prmValue = $prmValue->toString();
					}
					break;
				case 'Timestamp':
					if(defined('DATETIME_FORMAT')) {
						$prmValue = $prmValue->toFormatString(DATETIME_FORMAT);
					}
					elseif(defined('DATE_FORMAT')) {
						$prmValue = $prmValue->toFormatString(DATE_FORMAT);
					}
					else {
						$prmValue = $prmValue->toString();
					}
					break;
				case 'Time':
						$prmValue = $prmValue->toString();
					break;
				case (strpos(get_class($prmValue), 'Enum') !== false) :
					$prmValue = $prmValue->getId();
					break;

				default:
					if(method_exists($prmValue, 'toString')) {
						$methodName = "toString";
						$prmValue = $prmValue->{$methodName}();
					}
					elseif($prmValue instanceof IdentifiableObject) {
						$prmValue = (int)$prmValue->getId();
					}
			}
		}

		if($valueType)
			$prmValue = $prm->getImportFilter()->apply($prmValue);
		
		return $prmValue;

	}

	private function getFieldName($fieldName, $extraParams, $extraSettings)
	{

		if(!array_key_exists('clearName', $extraSettings)) {
			$fieldName = $this->scopeName ? $this->scopeName.'['.$fieldName.']' : $fieldName;
		}
		return $fieldName;
	}

	private function getFieldParams($fieldName, $extraParams, $extraSettings) {

		$fieldParams = array('name' => $this->getFieldName($fieldName, $extraParams, $extraSettings));
		$prmName = empty($extraSettings['prmName']) ? $fieldName : $extraSettings['prmName'];

		if(empty($extraSettings['noValue'])) {
			$fieldParams['value'] = $this->getFieldValue($prmName, $extraParams, $extraSettings);
		}

		$fieldParams = array_merge($fieldParams, $extraParams);
		
		return $fieldParams;
	}


	private function getFieldAttributesString($fName, $extraParams, $extraSettings)
	{
		$fieldParams = $this->getFieldParams($fName, $extraParams, $extraSettings);
		$attributesString = '';
		foreach($fieldParams as $paramName => $paramValue) {
			if(is_object($paramValue)) {
				$methodName = "get".ucfirst($fName);
				$attributesString .= " $paramName=\"{$paramValue->$methodName()}\"";
			}
			else {
				$attributesString .= " $paramName=\"$paramValue\"";
			}
		}

		return $attributesString;
	}


	/**
	 * Returns text field HTML
	 *
	 * @return string
	 */
	public function makeTextInput($fName, $extraParams = array(), $extraSettings = array())
	{
		$fieldUniqueParams = array('type' => 'text', 'class' => 'text');

		$extraParams['class'] = $this->makeCssClassString($extraParams, $fieldUniqueParams);

		unset($fieldUniqueParams['class']);
		$attributesString = $this->getFieldAttributesString($fName, array_merge($fieldUniqueParams, $extraParams), $extraSettings);
		
		$fieldFinalTemplate = "<input {$attributesString}>";

		if($this->fieldsWithErrors && !isset($extraSettings['noError'])) {
			$fieldFinalTemplate =
				$this->getFieldError($fName)
				."\n"
				.$fieldFinalTemplate;
		}

		return $fieldFinalTemplate;
	}


	/**
	 * Returns password field HTML
	 *
	 * @return string
	 */
	public function makePasswordInput($fName, $extraParams = array(), $extraSettings = array()) {
		$fieldUniqueParams = array('type' => 'password', 'class' => 'password');

		$extraParams['class'] = $this->makeCssClassString($extraParams, $fieldUniqueParams);

		return $this->makeTextInput($fName, array_merge($fieldUniqueParams, $extraParams), $extraSettings);
	}


	/**
	 * Returns checkbox field HTML
	 *
	 * @return string
	 */
	public function makeCheckboxInput($fName, $extraParams = array(), $extraSettings = array())
	{
		$fieldUniqueParams = array('type' => 'checkbox', 'class' => 'checkbox');

		$extraParams['class'] = $this->makeCssClassString($extraParams, $fieldUniqueParams);

		$fieldParams = $this->getFieldParams($fName, $extraParams, $extraSettings);
		$attributesString = $this->getFieldAttributesString($fName, array_merge($fieldUniqueParams, $extraParams), $extraSettings);

		$isChecked = false;
		if(
			(isset($extraSettings['checked']) && $fieldParams['value'] !== false)
			||
			$fieldParams['value'] === true
		) {
			$isChecked = true;
		}

		$fieldFinalTemplate = "<input {$attributesString} ".( $isChecked ? "checked=\"checked\"" : "").">";

		if($this->fieldsWithErrors && !isset($extraSettings['noError'])) {
			$fieldFinalTemplate =
				$this->getFieldError($fName)
				."\n"
				.$fieldFinalTemplate;
		}

		return $fieldFinalTemplate;
	}


	/**
	 * Returns textarea HTML
	 *
	 * @return string
	 */
	public function makeTextarea($fName, $extraParams = array(), $extraSettings = array())
	{
		$fieldUniqueParams = array('class' => 'textarea');

		$extraParams['class'] = $this->makeCssClassString($extraParams, $fieldUniqueParams);

		$fieldParams = $this->getFieldParams($fName, $extraParams, $extraSettings);

		$value = ArrayUtilsExtended::cutElement($fieldParams, "value");

		$attributesString = '';

		foreach($fieldParams as $paramName => $paramValue) {
			$attributesString .= " $paramName=\"$paramValue\"";
		}

		$fieldFinalTemplate = "<textarea {$attributesString}>{$value}</textarea>";

		if($this->fieldsWithErrors && !isset($extraSettings['noError'])) {
			$fieldFinalTemplate =
				$this->getFieldError($fName)
				."\n"
				.$fieldFinalTemplate;
		}

		return $fieldFinalTemplate;
	}


	/**
	 * Returns <select> field HTML
	 * @return string
	 */
	public function makeSelect($fName, $values = array(), $emptyOptionValue = null, $extraParams = array(), $extraSettings = array())
	{

		$fieldUniqueParams = array('class' => 'select');

		$extraParams['class'] = $this->makeCssClassString($extraParams, $fieldUniqueParams);

		$extraSettings['noValue'] = true;
		$attributesString = $this->getFieldAttributesString($fName, array_merge($fieldUniqueParams, $extraParams), $extraSettings);

		$selectedOptionValue = $this->getFieldValue($fName, $extraParams, $extraSettings);

		if(!empty($extraParams['value'])) {
			$selectedOptionValue = $extraParams['value'];
		}

		$fieldFinalTemplate = "<select {$attributesString}>";

		if($emptyOptionValue !== null) {
			$fieldFinalTemplate .= '<option value="">'. $emptyOptionValue .'</option>' . "\n";
		}

		foreach($values as $optionValue => $optionTitle) {
			if(isset($extraSettings['titleIsValue'])) {
				$optionValue = $optionTitle;
			}
			$fieldFinalTemplate .= '<option value="'. $optionValue .'" ' . ($selectedOptionValue === $optionValue ? "selected" : "") . '>' .$optionTitle. '</option>' . "\n";
		}

		$fieldFinalTemplate .= "</select>";

		if($this->fieldsWithErrors && !isset($extraSettings['noError'])) {
			$fieldFinalTemplate =
				$this->getFieldError($fName)
				."\n"
				.$fieldFinalTemplate;
		}

		return $fieldFinalTemplate;
	}


	/**
	 * Returns <select> field HTML
	 * @return string
	 */
	public function makeSelectFromTemplate($fName, $values = array(), $fieldTemplateName, $emptyOptionValue = null, $extraParams = array(), $extraSettings = array())
	{

		$fieldUniqueParams = array('class' => 'select');

		$extraSettings['noValue'] = true;
		$attributesString = $this->getFieldAttributesString($fName, array_merge($fieldUniqueParams, $extraParams), $extraSettings);

		$selectedOptionValue = $this->getFieldValue($fName, $extraParams, $extraSettings);

		if(!empty($extraParams['value'])) {
			$selectedOptionValue = $extraParams['value'];
		}

		$fieldParams = $this->getFieldParams($fName, $extraParams, $extraSettings);

		$fieldParams['class'] = $this->makeCssClassString($extraParams, $fieldUniqueParams);

		$fieldParams['value'] = $fieldParams['selectedValue'] = $fieldParams['selectedOptionValue'] = $selectedOptionValue;
		$fieldParams['selectValues'] = $values;

		$fieldTemplateName = isset($extraSettings['cleanTemplateName']) ? $fieldTemplateName : 'form/field.select.' . $fieldTemplateName;
		$fieldView = $this->viewResolver->resolveViewName($fieldTemplateName);

		$fieldFinalTemplate = $fieldView->toString($fieldParams);
		if($this->fieldsWithErrors && !isset($extraSettings['noError'])) {
			$fieldFinalTemplate =
				$this->getFieldError($fName)
				."\n"
				.$fieldFinalTemplate;
		}

		return $fieldFinalTemplate;
	}


	/**
	 * Returns text field HTML
	 *
	 * @return string
	 */
	public function makeFileInput($fName, $extraParams = array(), $extraSettings = array())
	{
		$fieldUniqueParams = array('type' => 'file', 'class' => 'file');

		$extraParams['class'] = $this->makeCssClassString($extraParams, $fieldUniqueParams);

		$attributesString = $this->getFieldAttributesString($fName, array_merge($fieldUniqueParams, $extraParams), $extraSettings);

		$fieldFinalTemplate = "<input {$attributesString}>";

		if($this->fieldsWithErrors && !isset($extraSettings['noError'])) {
			$fieldFinalTemplate =
				$this->getFieldError($fName)
				."\n"
				.$fieldFinalTemplate;
		}

		return $fieldFinalTemplate;
	}


	/**
	 * Returns hidden field HTML
	 *
	 * @return string
	 */
	public function makeHiddenInput($fName, $extraParams = array(), $extraSettings = array())
	{
		$fieldUniqueParams = array('type' => 'hidden', 'class' => 'checkbox');

		$extraParams['class'] = $this->makeCssClassString($extraParams, $fieldUniqueParams);

		$attributesString = $this->getFieldAttributesString($fName, array_merge($fieldUniqueParams, $extraParams), $extraSettings);

		$fieldFinalTemplate = "<input {$attributesString}>";

		return $fieldFinalTemplate;
	}


	/**
	 * Returns search field HTML
	 * @return string
	 */
	public function makeSearchField($fName, $extraParams = array(), $extraSettings = array())
	{
		$fieldUniqueParams = array('class' => 'search-field');
		$form = $this->form;

		if (is_array($fName)) {
			$extraSettings = array_merge($extraSettings, $fName);
			$fName = $extraSettings['name'];
			unset($extraSettings['name']);
		}

		$extraParams['class'] = $this->makeCssClassString($extraParams, $fieldUniqueParams);
		$fieldParams = $this->getFieldParams($fName, $extraParams, $extraSettings);

		$value = '';
		if($form->exists('search')) {
			$searchData = $form->getValue('search');

			if(isset($searchData[$fName])) {
				$value = $searchData[$fName];
				$fieldParams['value'] = $value;
			}
		}

		$attributesString = '';

		foreach($fieldParams as $paramName => $paramValue) {
			$attributesString .= " $paramName=\"$paramValue\"";
		}

		$fNameParts = explode('.', $fName);
		$fieldTemplateSuffix = 'default';

		if(count($fNameParts) > 2) {
			$fieldTemplateSuffix = $fNameParts[2];
		}

		if(isset($extraSettings['templateSuffix'])) {
			$fieldTemplateSuffix = $extraSettings['templateSuffix'];
		}

		$fieldTemplateName = 'field.' . $fieldTemplateSuffix;


		if($fieldTemplateSuffix == 'select') {
			$selectValues = array();
			if(isset($extraSettings['source']['values']) && is_array($extraSettings['source']['values'])) {
				$selectValues = $extraSettings['source']['values'];
				$fieldParams['selectValues'] = $selectValues;
			}
			elseif(isset($extraSettings['source']['enum'])) {
				$sourceEnum = $extraSettings['source']['enum'];

				if(method_exists($sourceEnum, 'getListForSearch')) {
					$selectValues = $sourceEnum::getListForSearch();
				}
				else {
					$selectValues = ArrayUtilsExtended::enumListToIndexedArray($sourceEnum::getList());
				}

				$fieldParams['selectValues'] = $selectValues;
			}
			else {
				$sourceEntity = $extraSettings['source']['entity'];
				$sourceEntityDAO = $sourceEntity::dao();
				$keyField = 'id';
				$valueField = 'name';

				if(isset($extraSettings['source']['keyField'])) {
					$keyField = $extraSettings['source']['keyField'];
				}
				if(isset($extraSettings['source']['valueField'])) {
					$valueField = $extraSettings['source']['valueField'];
				}

				if(method_exists($sourceEntityDAO, 'getListForSearch')) {
					$selectValues = $sourceEntityDAO->getListForSearch();
				}
				else {
					$selectValues = $sourceEntityDAO->getList();
				}

				$fieldParams['selectValues'] = ArrayUtilsExtended::objectListToSimpleIndexed($selectValues, $keyField, $valueField  );
			}
		}

		$fieldView = $this->viewResolver->resolveViewName('form/'.$fieldTemplateName);

		$fieldParams = array_merge($extraSettings, $fieldParams);
		return $fieldView->toString($fieldParams);
	}


	public function makeSearchFieldFromColumnData($columnData, $extraParams = array(), $extraSettings = array())
	{
		Assert::isArray($columnData, 'Column data must be an array');

		if(!empty($columnData['searchSkip'])) {
			return '';
		}

		$searchData = null;
		if(!isset($columnData['search'])) {
			$searchData = $columnData['name'];
		}
		else {
			$searchData = $columnData['search'];
		}

		if(!is_array($searchData)) {
			$searchData = array('name' => $searchData);
		}


		if(!isset($columnData['search']) || !is_array($columnData['search']) || isset($searchData['name'])) {
			$searchData = array($searchData);
		}

		$columnDataWithoutSearch = $columnData;
		if(isset($columnDataWithoutSearch['search'])) {
			unset($columnDataWithoutSearch['search']);
		}

		$finalSearchHtml = '';
		foreach ($searchData as $searchFieldData) {
			if(isset($searchFieldData['source'])) {
				$searchFieldData['templateSuffix'] = 'select';
			}
			$searchFieldData = array_merge($columnDataWithoutSearch, $searchFieldData);
			$finalSearchHtml .= $this->makeSearchField($searchFieldData);
		}

		return $finalSearchHtml;
	}


	/**
	 * Returns field error
	 *
	 * @return string
	 */
	public function getFieldError($prmName, $templateName = "default", $returnNameless = true)
	{
		$form = $this->form;
		if(!$form->exists($prmName) && !$form->ruleExists($prmName))
			return '';

		$errorText = '';

		while(true) {
			if($errorText = $form->getTextualErrorFor($prmName)) {
				break;
			}

			if($form->hasError($prmName) && $returnNameless) {
				switch($form->getError($prmName)) {
					case Form::MISSING:
						$templateName = 'namelessMissing';
						break;
					case Form::WRONG:
					default:
						$templateName = 'namelessWrong';
				}
				break;
			}

			return '';
		}

		$viewResolver =
			MultiPrefixPhpViewResolver::create()->
				addPrefix(DIR_TEMPLATES_TINY)
		;

		try {
			$viewResolver = WebApplication::me()->getViewResolver();
		} catch (BaseException $e) {/**/}

		$view = $viewResolver->resolveViewName('form/error' . ucfirst($templateName));

		$model = Model::create()->set('errorText', $errorText);

		return $view->toString($model);
	}


	/**
	 * Returns fields errors list
	 *
	 * @return string
	 */
	public function getFieldsErrors($tplName = "default")
	{
		
		$errorsList = array();
		foreach($this->form->getErrors() as $prmName => $errorType) {
			if($prmError = $this->getFieldError($prmName, $tplName)) {
				$errorsList[] = $prmError;
			}
		}
		
		return $errorsList;
	}


	/**
	 * @return Form
	 */
	public function getForm()
	{
		return $this->form;
	}


	/**
	 * @return static
	 */
	public function setForm($form)
	{
		$this->form = $form;
		return $this;
	}


	/**
	 * @return mixed
	 */
	protected function makeCssClassString($extraParams, $fieldUniqueParams)
	{
		$classString = '';
		if(!empty($fieldUniqueParams['class'])) {
			$classString = $fieldUniqueParams['class'];
		}

		if (!empty($extraParams['class'])) {
			$classString .= ' ' . $extraParams['class'];
		}

		return $classString;
	}
}
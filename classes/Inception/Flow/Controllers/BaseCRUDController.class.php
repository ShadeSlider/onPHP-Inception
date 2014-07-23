<?php
/**
 * @author Eric I. Gorbikov <ernest.gorbikov@gmail.com>
 * @copyright 2014 Eric I. Gorbikov <ernest.gorbikov@gmail.com>
 */

abstract class BaseCRUDController extends BaseController {

	const DEFAULT_ACTION_NAME = 'list';
	const DEFAULT_ACTION_METHOD_NAME = 'listAction';
	const PHYSICALLY_DELETE_ENTITY = false;

	const DEFAULT_LIST_LIMIT = 20;

	/** Related business entity name */
	protected $entityName;

	protected $autoFilledDatesMapping = array(
		'createdAt' => array('create'),
		'updatedAt' => array('update'),
		'deletedAt' => array('delete')
	);


	/**
	 * @return ModelAndView
	 */
	public function handleRequest(HttpRequest $request)
	{
		$this->entityName = $this->resolveEntityName($request);

		$this->mav = parent::handleRequest($request);

		if($this->getEntityName()) {
			$entityNameDashed = StringUtils::camelCaseToDashed($this->getEntityName());
			$this->mav->
				getModel()->
					set('entityName', $this->getEntityName())->
					set('entityNameDashed', $entityNameDashed)
			;
		}

		return $this->mav;
	}


	/**
	 * @return string
	 * @throws ClassNotFoundException
	 */
	protected function resolveEntityName(HttpRequest $request)
	{
		$entityName = '';

		while(true) {
			if(defined('static::ENTITY_NAME')) {
				$entityName = static::ENTITY_NAME;
				break;
			}

			if($request->hasAttachedVar('entityName')) {
				$entityName = $request->getAttachedVar('entityName');
				break;
			}

			$entityName = str_replace('CRUD', '', str_replace('Controller', '', get_class(new static)));
			break;
		}

		if(!class_exists($entityName) && !class_exists($entityName . 'Entity')) {
			throw new ClassNotFoundException('Cannot resolve entity class ' . $entityName);
		}

		return $entityName;
	}


	/**
	 * @return string
	 */
	public function getEntityName()
	{
		return $this->entityName;
	}



	/**************************************************************
	 ************************ LIST ACTION *************************
	 **************************************************************/
	/**
	 * @return ModelAndView
	 */
	protected function listAction(HttpRequest $request)
	{
		$entity = $this->makeEntity();
		$entityDao = $entity->dao();
		$searchFilter = $this->makeSearchFilter($request);

		$currentActionUCFirst = ucfirst($this->currentAction);
		if(method_exists($this, 'makeListCriteria' . $currentActionUCFirst)) {
			$criteria = $this->{'makeListCriteria' . $currentActionUCFirst}($entityDao);
		}
		else {
			$criteria = $this->makeListCriteria($entityDao);
		}

		if(method_exists($this, 'applySearchFilterToCriteria' . $currentActionUCFirst)) {
			$criteria = $this->{'applySearchFilterToCriteria' . $currentActionUCFirst}($criteria, $searchFilter);
		}
		else {
			$criteria = $this->applySearchFilterToCriteria($criteria, $searchFilter);
		}

		$pager = $this->makePager($request, $criteria, $searchFilter);


		if(method_exists($this, 'getEntityList' . $currentActionUCFirst)) {
			$entityList = $this->{'getEntityList' . $currentActionUCFirst}($criteria);
		}
		else {
			$entityList = $this->getEntityList($criteria);
		}

		$formSearch = $this->importFormDataFromRequestIntoForm($request, $this->makeForm('list'), self::SEARCH_DATA_SCOPE_NAME);
		$formSearch->dropAllErrors();

		$formSearch->import(array('search' => $searchFilter));

		$this->mav->
			getModel()->
				set('pager', $pager)->
				set('searchFilter', $searchFilter)->
				set('list', $entityList)->
				set('entityList', $entityList)->
				set('formHelperSearch', FormHelper::create($formSearch, self::SEARCH_DATA_SCOPE_NAME))
			;

		return $this->mav;
	}


	/**
	 * @return Form
	 */
	protected function makeFormListAction()
	{
		$form =
			Form::create()->
				add(
					Primitive::set(self::SEARCH_DATA_SCOPE_NAME)->
						addImportFilter(Filter::trim())->
						setDefault(array())
				)
		;

		return $form;
	}


	/**
	 * @return array
	 */
	protected function makeSearchFilter(HttpRequest $request)
	{
		$searchFilterForm = $this->importFormDataFromRequestIntoForm($request, $this->makeForm('list'));
		$searchFilter = $searchFilterForm->getValueOrDefault(self::SEARCH_DATA_SCOPE_NAME);

		$pagerDataForm =
			Form::create()->add(Primitive::integer('offset'))
		;

		$searchFilterForm = $this->importFormDataFromRequestIntoForm($request, $pagerDataForm);

		if($searchFilterForm->getValue('offset')) {
			$searchFilter['offset'] = $searchFilterForm->getValue('offset');
		}

		$searchFilter = array_filter($searchFilter, 'strlen');

		return $searchFilter;
	}


	/**
	 * @return Criteria
	 */
	protected function makeListCriteria($entityDao)
	{
		return Criteria::create($entityDao);
	}


	/**
	 * @return Criteria
	 */
	protected function applySearchFilterToCriteria(Criteria $criteria, array $filterData)
	{
		$criteria->setLimit(static::DEFAULT_LIST_LIMIT);

		if(isset($filterData['limit'])) {
			$limit = $filterData['limit'];
			unset($filterData['limit']);

			$criteria->setLimit($limit);
		}
		if(isset($filterData['offset'])) {
			$offset = $filterData['offset'];
			unset($filterData['offset']);

			$criteria->setOffset($offset);
		}
		if(isset($filterData['page'])) {
			$offset = $filterData['page'] * static::DEFAULT_LIST_LIMIT;
			unset($filterData['page']);

			$criteria->setOffset($offset);
		}

		$affectedFields = array();
		foreach ($filterData as $fieldName => $fieldValue) {
			if($fieldValue == '') {
				continue;
			}

			if(strpos($fieldName, '.') !== false) {
				$fieldNameParts = explode('.', $fieldName);
				$fieldType = 'string';

				if(count($fieldNameParts) > 2) {
					list($cleanFieldName, $fieldLogic, $fieldType) = $fieldNameParts;
				}
				else {
					list($cleanFieldName, $fieldLogic) = $fieldNameParts;
				}

				$cleanFieldName = str_replace('_', '.', $cleanFieldName);

				switch($fieldType) {
					case 'date':
						$cleanFieldName = $criteria->getDao()->guessAtom($cleanFieldName, $criteria->toSelectQuery());
						$cleanFieldName->castTo('Date');
						$fieldValue = Date::create($fieldValue)->toString();
						if(!in_array($fieldLogic, array('min', 'max', 'mineq', 'maxeq'))) {
							$fieldLogic = 'eq';
						}
						break;
					case 'bool':
						$fieldValue = (bool)$fieldValue;
						$fieldLogic = $fieldValue ? 'true' : 'false';
						break;
				}

				switch($fieldLogic) {
					case 'min':
						$criteria->add(Expression::gt($cleanFieldName, $fieldValue));
						break;
					case 'max':
						$criteria->add(Expression::lt($cleanFieldName, $fieldValue));
						break;
					case 'mineq':
						$criteria->add(Expression::gtEq($cleanFieldName, $fieldValue));
						break;
					case 'maxeq':
						$criteria->add(Expression::ltEq($cleanFieldName, $fieldValue));
						break;
					case 'eq':
						$criteria->add(Expression::eq($cleanFieldName, $fieldValue));
						break;
					case 'true':
						$criteria->add(Expression::isTrue($cleanFieldName));
						break;
					case 'false':
						$criteria->add(Expression::isFalse($cleanFieldName));
						break;
					case 'ilike':
						$criteria->add(Expression::ilike($cleanFieldName, '%'.$fieldValue.'%'));
						break;
					default:
						if(is_numeric($fieldValue)) {
							$criteria->add(Expression::eq($cleanFieldName, $fieldValue));
						}
						else {
							$criteria->add(Expression::ilike($cleanFieldName, '%'.$fieldValue.'%'));
						}
				}
				$affectedFields[] = $cleanFieldName;
			}
			else {
				$fieldName = str_replace('_', '.', $fieldName);
				if(is_numeric($fieldValue)) {
					$criteria->add(Expression::eq($fieldName, $fieldValue));
				}
				else {
					$criteria->add(Expression::ilike($fieldName, '%'.$fieldValue.'%'));
				}
				$affectedFields[] = $fieldName;
			}


		}

		if(method_exists($this->getEntityName(), 'getIsDeleted') && !in_array('isDeleted', $affectedFields)) {
			$criteria->add(Expression::isFalse('isDeleted'));
		}

		if(is_array($this->defaultListOrderBy)) {
			$criteria->dropOrder();
			foreach ($this->defaultListOrderBy as $orderByData) {

				if(is_array($orderByData)) {
					$orderBy = OrderBy::create($orderByData['field']);
					if(isset($orderByData['desc'])) {
						$orderBy->desc();
					}
				}
				else {
					$orderBy = OrderBy::create($orderByData);
				}

				$criteria->addOrder($orderBy);
			}
		}

		return $criteria;
	}


	/**
	 * @return Pager
	 */
	protected function makePager(HttpRequest $request, Criteria $criteria, array $filterData)
	{
		if(isset($filterData['offset'])) {
			unset($filterData['offset']);
		}

		$settings = array(
			'limit' => $criteria->getLimit(),
			'offset' => $criteria->getOffset(),
			'count' => $criteria->getResult()->getCount(),
			'router' => $this->routeName,
			'urlParameters' => array_merge(
				$request->getGet(),
				$request->getPost(),
				$request->getAttached(),
				array(
					'action' => $this->getCurrentAction()
				)
			),
			'extraUrlParameters' => array(static::SEARCH_DATA_SCOPE_NAME => array_filter($filterData, 'strlen'))
		);


		$pager = PagerUtils::makePager($settings);

		return $pager;
	}


	/**
	 * @return array
	 */
	protected function getEntityList(Criteria $criteria)
	{
		return $criteria->getList();
	}
	/*************************************************************/



	/**************************************************************
	 ************************ VIEW ACTION *************************
	 **************************************************************/
	protected function viewAction(HttpRequest $request)
	{
		$mav = $this->mav;
		$model = $mav->getModel();

		$idForm = $this->makeIdForm();
		$idForm = $this->importFormDataFromRequestIntoForm($request, $idForm);

		/** @var BaseStorable $entity */
		$entity = $idForm->getValue('id');

		if($entity === null) {
			$mav->setView($this->makeRedirectHomeView());
			return $mav;
		}

		$entityData = $entity->getDisplayableData();
		foreach ($entityData as $propertyName => $propertyValue) {
			$model->set($propertyName, $propertyValue);
		}

		$model->
			set('entity', $entity)->
			set('entityData', $entityData)

		;

		return $mav;
	}
	/**************************************************************/



	/**************************************************************
	 ************************ EDIT ACTION *************************
	 **************************************************************/
	protected function editAction(HttpRequest $request)
	{
		$mav = $this->mav;
		$model = $mav->getModel();
		$idForm = $this->makeIdForm();
		$idForm = $this->importFormDataFromRequestIntoForm($request, $idForm);

		$form = $this->makeForm($idForm->getValue('id') ? 'update' : 'create');

		if($idForm->getValue('id')) {
			FormUtils::object2form($idForm->getValue('id'), $form);
		}

		$mav->setView($this->getCurrentActionMethodName());
		$model->
			set('form', $form)->
			set('formHelper', FormHelper::create($form, $this->makeDataScope()))
		;

		return $this->mav;
	}
	/**************************************************************/



	/**************************************************************
	 *********************** CREATE ACTION ************************
	 **************************************************************/
	protected function createAction(HttpRequest $request)
	{
		return $this->saveAction($request, 'create');
	}


	/**************************************************************
	 *********************** UPDATE ACTION ************************
	 **************************************************************/
	protected function updateAction(HttpRequest $request)
	{
		return $this->saveAction($request, 'update');
	}


	/**************************************************************
	 ************************* SAVE ACTION ************************
	 **************************************************************/
	protected function saveAction(HttpRequest $request, $actualActionName)
	{
		$mav = $this->mav;
		$form = $this->makeForm($actualActionName);


		$form = $this->importFormDataFromRequestIntoForm($request, $form);

		if($actualActionName == 'create' && $form->exists('id')) {
			$form->markGood('id');
		}

		if($form->getErrors()) {
			$mav->
				setView('editAction')->
				getModel()->
					set('form', $form)->
					set('formHelper', FormHelper::create($form, $this->makeDataScope()))
			;
			return $mav;
		}

		$entity = $this->makeEntity($form->exists('id') ? $form->getValue('id') : null);


		FormUtils::form2object($form, $entity);

		foreach($this->getAutoFilledDatesMapping() as $fieldName => $actions) {
			$dateSetterName = 'set' . ucfirst($fieldName);
			if(
				method_exists($entity, $dateSetterName)
				&&
				in_array($this->getCurrentAction(), $actions)
			) {
				$entity->$dateSetterName(TimestampTZ::makeNow());
			}
		}

		$entity->dao()->take($entity);


		$messageName = 'message' . ucfirst($actualActionName) . 'd';
		$message = $this->getControllerMetaDataValueOrDefault($messageName, 'Entity ' . $actualActionName . 'd');
		$this->flashMessagesManager->addMessage('crudMessage', $message);

		$mav->setView(
			CleanRedirectView::create(
				RouterUrlHelper::url(
					array(
						SYSTEM_CONTROLLER_VAR_NAME => StringUtils::camelCaseToDashed($this->getCleanName()),
						'action' => 'list'
					),
					'entity',
					true
				)
			)
		);

		return $mav;
	}



	/**************************************************************
	 *********************** DELETE ACTION ************************
	 **************************************************************/
	protected function deleteAction(HttpRequest $request)
	{
		$idForm = $this->makeIdForm();
		$idForm = $this->importFormDataFromRequestIntoForm($request, $idForm);

		if(!$idForm->getErrors()) {
			try {
				$entity = $idForm->getValue('id');

				while(true) {

					if(!$entity) {
						break;
					}

					if(method_exists($entity, 'setIsDeleted') && !static::PHYSICALLY_DELETE_ENTITY) {
						$entity->setIsDeleted(true);

						if(method_exists($entity, 'setDeletedAt')) {
							$entity->setDeletedAt(TimestampTZ::makeNow());
						}

						$entity->dao()->take($entity);
						break;
					}

					$entity->dao()->dropById($entity->getId());
					break;
				}

			} catch (BaseException $e) {
				/* No action needed. */
			}
		}

		$message = $this->getControllerMetaDataValueOrDefault('messageDeleted', 'Entity deleted');
		$this->flashMessagesManager->addMessage('crudMessage', $message);

		$this->mav->setView(
			CleanRedirectView::create(
				RouterUrlHelper::url(
					array(
						SYSTEM_CONTROLLER_VAR_NAME => StringUtils::camelCaseToDashed($this->getCleanName()),
						'action' => 'list'
					),
					'entity'
				)
			)
		);

		return $this->mav;
	}


	/**
	 * @throws BadMethodCallException
	 * @return Form
	 */
	protected function makeForm($action = null, $allowDefault = true)
	{
		$formMethodName = $this->makeFormMethodName($action);

		$form = null;
		$entityProto = $this->getEntityProto();

		while(true) {
			if(method_exists($this, $formMethodName)) {
				$form = $this->{$formMethodName}();
				break;
			}

			if(method_exists($this, $formMethodName . 'Action')) {
				$form = $this->{$formMethodName . 'Action'}();
				break;
			}

			if(method_exists($entityProto, $formMethodName)) {
				$form = $entityProto->{$formMethodName}();
				break;
			}

			if(method_exists($entityProto, $formMethodName . 'Action')) {
				$form = $entityProto->{$formMethodName . 'Action'}();
				break;
			}

			if($allowDefault) {
				$form = $entityProto->makeForm();
				break;
			}

			throw new BadMethodCallException('Cannot make form using method name "' . $formMethodName . '"');
			break;
		}

		$this->form = $form;

		return $form;
	}


	/**
	 * @return StorableDAO
	 */
	protected function getEntityDao()
	{
		return call_user_func($this->entityName . '::dao');
	}


	/**
	 * @return AbstractProtoClass
	 */
	protected function getEntityProto()
	{
		return call_user_func($this->entityName . '::proto');
	}


	/**
	 * @return string
	 */
	protected function makeFormMethodName($action = null)
	{
		if(!$action) {
			$action = $this->getCurrentAction();
		}
		$formMethodName = 'makeForm' . ucfirst($action);
		return $formMethodName;
	}


	/**
	 * @return IdentifiableObject
	 */
	protected function makeEntity($entity = null)
	{
		$entityName = $this->entityName;

		if(!$entity instanceof $entityName) {
			$entity = $entityName::create();
		}

		return $entity;
	}


	/**
	 * @return array
	 */
	public function getAutoFilledDatesMapping()
	{
		return $this->autoFilledDatesMapping;
	}


	/**
	 * @return BaseCRUDController
	 */
	public function setAutoFilledDatesMapping($autoFilledDatesMapping)
	{
		$this->autoFilledDatesMapping = $autoFilledDatesMapping;
		return $this;
	}


	/**
	 * @return string
	 */
	public function getCleanName()
	{
		$cleanName = parent::getCleanName();
		return str_replace('CRUD', '', $cleanName);
	}


	/**
	 * @return Form
	 */
	protected function makeIdForm()
	{
		return Form::create()->
			add(
				Primitive::identifier('id')->of($this->getEntityName())->required()
			);
	}
}
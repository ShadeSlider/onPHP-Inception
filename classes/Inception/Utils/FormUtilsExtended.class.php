<?php
	class FormUtilsExtended extends FormUtils {

		protected static $importFiltersMap = array(
			'PrimitiveString' => 'textImport'
		);


		/**
		 * Drops all primitives from Form except the given ones.
		 * @return Form
		 */
		public static function clipForm(Form $form, $resultPrimitivesList, $inverse = false) {

			foreach($form->getPrimitiveNames() as $pidx => $prName) {
				if((in_array($prName, $resultPrimitivesList) && $inverse) || (!in_array($prName, $resultPrimitivesList) && !$inverse)) {
					$form->drop($prName);
				}
			}

			return $form;
		}


		/**
		 * @return Form
		 */
		public static function applyImportFiltersToForm(Form $form, $filterMapOverride = array(), $filterMapAddition = array())
		{
			$filterMap = array_merge(self::$importFiltersMap, $filterMapOverride);

			/** @var FiltrablePrimitive $primitive */
			foreach($form->getPrimitiveList() as $primitive) {

				$filtersMethodName = null;
				$filtersExtraMethodName = null;

				if(!empty(self::$importFiltersMap[get_class($primitive)])) {
					$filtersMethodName = self::$importFiltersMap[get_class($primitive)];
				}

				if(!empty($filterMapOverride[get_class($primitive)])) {
					$filtersMethodName = $filterMapOverride[get_class($primitive)];
				}

				if(!empty($filterMapOverride[$primitive->getName()])) {
					$filtersMethodName = $filterMapOverride[$primitive->getName()];
				}

				if(!empty($filterMapAddition[$primitive->getName()])) {
					$filtersExtraMethodName = $filterMapAddition[$primitive->getName()];
				}

				$finalFilterChain = FilterChain::create();
				if($filtersMethodName !== null) {
					$filterChain = FilterFactory::$filtersMethodName();
					if(!$filterChain instanceof FilterChain) {
						$filterChain = FilterChain::create()->add($filterChain);
					}
					$finalFilterChain->merge($filterChain);
				}
				if($filtersExtraMethodName !== null) {
					$filterChain = FilterFactory::$filtersExtraMethodName();
					if(!$filterChain instanceof FilterChain) {
						$filterChain = FilterChain::create()->add($filterChain);
					}
					$finalFilterChain->merge($filterChain);
				}

				if(!$finalFilterChain->isEmpty()) {
					$primitive->setImportFilter($finalFilterChain);
				}
			}

			return $form;
		}


		/**
		 * @return array
		 */
		public static function objectListToFormList($objectList, $formList)
		{
			Assert::isEqual(count($objectList), count($formList));

			$objectList = array_values($objectList);

			foreach($objectList as $idx => $object) {
				$form = $formList[$idx];

				static::object2form($object, $form);
			}

			return $formList;
		}


		/**
		 * @return PrimitiveListOfForms
		 */
		public static function importObjectListToPrimitiveListOfForms($objectList, PrimitiveListOfForms $formListOfFormsPrm)
		{
			$formList = array();
			foreach($objectList as $object) {
				$form = clone $formListOfFormsPrm->getForm();

				static::object2form($object, $form);

				$formList[] = $form;
			}

			$formListOfFormsPrm->importValue($formList);

			return $formListOfFormsPrm;
		}


		public static function formToPrimitivesArray(Form $form, array &$array)
		{
			foreach ($form->getPrimitiveList() as $name => $prm) {
				$array[$name] = $prm;
			}
		}
	}
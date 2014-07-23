<?php
/**
 * @author Eric I. Gorbikov <ernest.gorbikov@gmail.com>
 * @copyright 2014 Eric I. Gorbikov <ernest.gorbikov@gmail.com>
 */

class CommonCRUDController extends BaseCRUDController {

	protected $defaultListOrderBy = array('name', 'id');

	protected function listAction(HttpRequest $request)
	{
		$this->mav = parent::listAction($request);

		try {
			$formCreate = $this->makeForm('create');

			$this->mav->
				getModel()->
				set('form', $formCreate)->
				set('formHelper', FormHelper::create($formCreate, $this->makeDataScope()))
			;
		}
		catch(BadMethodCallException $e) {
			/* no creation form */
		}

		return $this->mav;
	}


} 
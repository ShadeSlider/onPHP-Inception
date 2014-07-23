<?php

final class AccessDeniedController extends BaseController
{

	public function defaultAction(HttpRequest $request)
	{
		$mav = $this->mav;
		$mav->setView('403');

		if(AjaxUtils::me()->isAjaxRequest($request)) {
			$mav->setView('403Ajax');
		}

		$mav->
			getModel()->
			set('httpStatus', HttpStatus::CODE_403);

		return $mav;
	}

	public function chooseAction(HttpRequest $request)
	{
		return static::DEFAULT_ACTION_NAME;
	}


}
<?php

final class NotFoundController extends BaseController
{
	const ALLOW_ACCESS_ALL = true;

	public function defaultAction(HttpRequest $request)
	{
		$mav = $this->mav;
		$mav->setView('404');

		if(AjaxUtils::me()->isAjaxRequest($request)) {
			$mav->setView('404Ajax');
		}

		$mav->
			getModel()->
				set('httpStatus', HttpStatus::CODE_404);

		return $mav;
	}
}
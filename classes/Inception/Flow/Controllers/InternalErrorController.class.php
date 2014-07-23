<?php

final class InternalErrorController extends BaseController
{
	const ALLOW_ACCESS_ALL = true;

	public function defaultAction(HttpRequest $request)
	{
		$mav = $this->mav;
		$mav->setView('500');

		$mav->
			getModel()->
				set('httpStatus', HttpStatus::CODE_500);

		return $mav;
	}
}
	<?php
class IndexController extends BaseController
{
	const ALLOW_ACCESS_ALL = true;

	public function defaultAction(HttpRequest $request)
	{
		return $this->mav;
	}
}
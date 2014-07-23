<?php

final class AuthController extends BaseController
{
	const DEFAULT_ACTION_NAME = 'showLogin';
	const DEFAULT_ACTION_METHOD_NAME = 'showLoginAction';

	const ALLOW_ACCESS_ALL = true;

	public function showLoginAction(HttpRequest $request)
	{
		$mav = $this->mav;
		$model = $mav->getModel();

		if(!$this->accessManager->isUserAuthenticated()) {
			$mav->setView('showLoginAction');
			$form = BackendUser::proto()->makeFormLogin();
			$model->set('formHelper', FormHelper::create($form));
		}
		else {
			$mav->setView($this->makeRedirectHomeView());
		}

		return $mav;
	}


	public function loginAction(HttpRequest $request)
	{
		$mav = $this->mav;
		$model = $mav->getModel();
		$mav->setView('showLoginAction');

		$form = BackendUser::proto()->makeFormLogin();

		$this->importFormDataFromRequestIntoForm($request, $form);

		if($form->getErrors()) {
			$model->set('formHelper', FormHelper::create($form));
			return $mav;
		}

		$rememberMe = $form->getValue('remember');

		$this->accessManager->authenticate($form->getValue('login'), $form->getValue('password'), $rememberMe);

		if(!$requestedUrl = Session::get('requestedUrl')) {
			$requestedUrl = PATH_WEB;
		}

		$mav->setView(CleanRedirectView::create($requestedUrl));

		return $mav;
	}


	public function logoutAction(HttpRequest $request)
	{
		$mav = $this->mav;
		$model = $mav->getModel();

		$this->accessManager->logout();

		$requestedUrl = PATH_WEB;
		$mav->setView(CleanRedirectView::create($requestedUrl));

		return $mav;
	}
}
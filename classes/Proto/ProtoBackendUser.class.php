<?php
/*****************************************************************************
 *   Copyright (C) 2006-2009, onPHP's MetaConfiguration Builder.             *
 *   Generated by onPHP-1.1.master at 2014-04-11 12:53:24                    *
 *   This file will never be generated again - feel free to edit.            *
 *****************************************************************************/

class ProtoBackendUser extends AutoProtoBackendUser {

	public function makeForm($prefix = null)
	{
		$form = parent::makeForm($prefix);

		$form->add(
			Primitive::string('passwordConfirm')->required()
		);
		return $form;
	}


	public function makeFormCreate($prefix = null)
	{
		$form = $this->makeForm($prefix);
		$form->drop('id');

		FormUtilsExtended::applyImportFiltersToForm($form);

		$this->applyRulesToForm($form);
		$form->dropRuleByName('emptyPasswordsIgnoreOnUpdate');

		return $form;
	}

	public function makeFormUpdate($prefix = null)
	{
		$form = $this->makeForm($prefix);

		FormUtilsExtended::applyImportFiltersToForm($form);

		$this->applyRulesToForm($form);

		return $form;
	}

	protected function applyRulesToForm(Form $form)
	{
		$form->addRule('passwordMismatch', CallbackLogicalObject::create(function(Form $form) {
			$password = $form->getValue('password');
			$passwordConfirm = $form->getValue('passwordConfirm');

			if($password != $passwordConfirm) {
				return false;
			}

			return true;
		}));
		$form->addWrongLabel('passwordMismatch', 'Password do not match');

		$form->addRule('emptyPasswordsIgnoreOnUpdate', CallbackLogicalObject::create(function(Form $form) {
			$password = $form->getValue('password');
			$passwordConfirm = $form->getValue('passwordConfirm');

			if($password == $passwordConfirm && ($password == '' && $passwordConfirm == '')) {
				$form->markGood('password');
				$form->markGood('passwordConfirm');
			}

			return true;
		}));

		$form->checkRules();
	}

	public function makeFormLogin()
	{
		$form = FormUtilsExtended::clipForm($this->makeForm(), array('login', 'password'));

		$form->add(Primitive::boolean('remember')->setDefault(false));

		$form->addRule('userExists', CallbackLogicalObject::create(function(Form $form){
			//Don't even check
			if($form->getErrors()) {
				return true;
			}

			$login = $form->getValue('login');
			$password = $form->getValue('password');

			$backendUser = BackendUser::dao()->getByLoginAndPassword($login, $password);

			if(!$backendUser) {
				$backendUser = BackendUser::dao()->getByEmailAndPassword($login, $password);
			}

			return $backendUser instanceof BackendUser;
		}));

		$form->addWrongLabel('userExists', 'Wrong login or password');

		return $form;
	}
}
<?php
/***************************************************************************
 *   Copyright (C) 2008 by Alexey Denisov                                  *
 *   adenisov@fjproject.ru                                                 *
 ***************************************************************************/

	class AjaxUtils extends Singleton implements Instantiatable
	{
		private static $ajaxRequestVar = 'HTTP_X_REQUESTED_WITH';
		private static $ajaxRequestValue = 'XMLHttpRequest';

		private $ajaxTested = false;
		private $isAjaxRequest = false;

		/**
		 * @return AjaxUtils
		 * @deprecated, please use me()
		 */
		public static function create()
		{
			return self::getInstance('AjaxUtils');
		}

		/**
		 * @return AjaxUtils
		 */
		public static function me()
		{
			return self::getInstance(__CLASS__);
		}

		/**
		 * @return boolean
		 */
		public function isAjaxRequest(HttpRequest $request)
		{
			if ($this->ajaxTested == false) {
				$form = Form::create()->
					add(
						Primitive::choice(self::$ajaxRequestVar)->
							setList(
								array(self::$ajaxRequestValue => self::$ajaxRequestValue)
							)
					)->
					import($request->getServer());
				if (!$form->getErrors() && $form->getValue(self::$ajaxRequestVar))
					$this->isAjaxRequest = true;
				else
					$this->isAjaxRequest = false;
			}
			return $this->isAjaxRequest;
		}
	}

?>

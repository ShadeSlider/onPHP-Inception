<?php
/**
 * @author Eric I. Gorbikov <ernest.gorbikov@gmail.com>
 * @copyright 2014 Eric I. Gorbikov <ernest.gorbikov@gmail.com>
 */

class FlashMessageManager {

	const SESSION_VAR_NAME = 'flash_messages';


	public static function create()
	{
		return new static;
	}


	/**
	 * @return FlashMessageManager
	 * @throws SessionNotStartedException
	 */
	public function addMessages(array $messages)
	{
		foreach($messages as $name => $msg) {
			$this->addMessage($name, $msg);
		}

		return $this;
	}


	/**
	 * @return FlashMessageManager
	 * @throws SessionNotStartedException
	 */
	public function addMessage($name, $msg)
	{
		$messages = $this->getList();
		$messages[$name] = $msg;


		$this->saveMessages($messages);

		return $this;
	}


	/**
	 * @return string|null
	 * @throws SessionNotStartedException
	 * @throws WrongArgumentException
	 */
	public function getMessage($name)
	{
		$messages = $this->getList();
		if(isset($messages[$name])) {

			$msg = $messages[$name];
			unset($messages[$name]);

			$this->saveMessages($messages);

			return $msg;
		}

		return null;
	}


	/**
	 * @return array
	 * @throws SessionNotStartedException
	 * @throws WrongArgumentException
	 */
	public function getList()
	{
		if(Session::exist(static::SESSION_VAR_NAME)) {
			return Session::get(static::SESSION_VAR_NAME);
		}

		return array();
	}


	/**
	 * @return FlashMessageManager
	 * @throws SessionNotStartedException
	 */
	protected function saveMessages(array $messages)
	{
		Session::assign(static::SESSION_VAR_NAME, $messages);

		return $this;
	}

} 
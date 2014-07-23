<?php
/**
 * @author Eric I. Gorbikov <ernest.gorbikov@gmail.com>
 * @copyright 2014 Eric I. Gorbikov <ernest.gorbikov@gmail.com>
 */
abstract class Config implements IConfig
{

	protected  $vars = array();
	protected  $isLocked = false;


	/**
	 * @return Config
	 */
	public static function create()
	{
		return new static;
	}


	/**
	 * @return Config
	 * @throws WrongStateException
	 */
	public function setSetting($name, $value)
	{
		Assert::isString($name);

		if($this->isLocked)
			throw new WrongStateException('Config is already locked.');

		$this->vars[$name] = $value;

		return $this;
	}


	/**
	 * @return Config
	 */
	public function addSettings(array $settings)
	{
		foreach($settings as $name => $value) {
			Assert::isString($name);
			$this->setSetting($name, $value);
		}

		return $this;
	}


	/**
	 * @return Config
	 * @throws WrongStateException
	 */
	public function setSettings(array $settings)
	{
		if($this->isLocked)
			throw new WrongStateException('Config is already locked.');

		$this->vars = array();
		$this->addSettings($settings);

		return $this;
	}


	/**
	 * @return Config
	 * @throws WrongStateException
	 */
	public function dropSetting($name)
	{
		Assert::isString($name);

		if($this->isLocked)
			throw new WrongStateException('Config is already locked.');

		if(isset($this->vars[$name])) {
			unset($this->vars[$name]);
		}

		return $this;
	}


	/**
	 * @return mixed
	 * @throws WrongArgumentException
	 */
	public function getSetting($name)
	{
		Assert::isString($name);

		if(!isset($this->vars[$name]))
			throw new WrongArgumentException('Setting ' . $name . 'is not set.');

		return $this->vars[$name];
	}


	/**
	 * @return Config
	 */
	public function lock()
	{
		$this->isLocked = true;

		return $this;
	}
} 
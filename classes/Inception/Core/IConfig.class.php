<?php
/**
 * @author Eric I. Gorbikov
 * @copyright 2014 Eric I. Gorbikov
 */
interface IConfig
{
	/**
	 * @return Config
	 */
	public function lock();

	/**
	 * @return mixed
	 * @throws WrongArgumentException
	 */
	public function getSetting($name);

	/**
	 * @return Config
	 */
	public function addSettings(array $settings);

	/**
	 * @return Config
	 * @throws WrongStateException
	 */
	public function setSetting($name, $value);

	/**
	 * @return Config
	 * @throws WrongStateException
	 */
	public function dropSetting($name);

	/**
	 * @return Config
	 * @throws WrongStateException
	 */
	public function setSettings(array $settings);

}
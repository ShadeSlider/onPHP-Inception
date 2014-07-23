<?php
/**
 * @author e.gorbikov
 * @copyright 2014 e.gorbikov
 */

class WebApplicationConfig extends Config {

	const NOT_FOUND_CONTROLLER_VAR = 'notFoundControllerName';
	const AUTH_CONTROLLER_VAR = 'authControllerName';
	const INTERNAL_ERROR_CONTROLLER_VAR = 'internalErrorControllerName';

	public function __construct()
	{
		$this->setSetting(static::NOT_FOUND_CONTROLLER_VAR, DEFAULT_NOT_FOUND_CONTROLLER_NAME);
		$this->setSetting(static::AUTH_CONTROLLER_VAR, DEFAULT_AUTH_CONTROLLER_NAME);
		$this->setSetting(static::INTERNAL_ERROR_CONTROLLER_VAR, DEFAULT_INTERNAL_ERROR_CONTROLLER_NAME);
	}
}
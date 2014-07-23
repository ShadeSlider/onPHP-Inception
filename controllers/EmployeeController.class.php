<?php
/**
 * @author Eric I. Gorbikov <ernest.gorbikov@gmail.com>
 * @copyright 2014 Eric I. Gorbikov <ernest.gorbikov@gmail.com>
 */

final class EmployeeController extends CommonCRUDController
{
	protected $defaultListOrderBy = array('lastName', 'id');
}
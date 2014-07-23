<?php
/**
 * @author Eric I. Gorbikov <ernest.gorbikov@gmail.com>
 * @copyright 2009-2014 Eric I. Gorbikov <ernest.gorbikov@gmail.com>
 */
class PagerUtils extends StaticFactory {

	/**
	 *  DEFAULT PARAMS
	 */
	protected static $defaultSetting = array(
		'limit' => 20,
		'offset' => 0,
		'count' => 0,
		'urlParameters' => array(),
		'extraUrlParameters' => array(),
		'urlPrefix' => 'offset',
		'onClick' => ''
	);


     /**
     * Generate Pager object
      *
     * @return Pager
     */
	public static function makePager($params = array(), $pagerName = "PagerDefault") {


		extract(static::$defaultSetting);
		extract($params);

		//Pager
		$pager = Pager::create($limit, $pagerName)->
        setCurrentPage($offset)->
        setUrlParameters($urlParameters)->
        setExtraUrlParameters($extraUrlParameters)->
        setOnClick($onClick)->
        setUrlPrefix($urlPrefix)->
        setTotalElement($count);


        if(!empty($router)) {
            $pager->setRouter($router);
        }
        //@END Pager


        return $pager;
	}
}
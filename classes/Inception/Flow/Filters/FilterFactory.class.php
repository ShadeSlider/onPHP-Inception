<?php
/**
 * @author Eric I. Gorbikov <ernest.gorbikov@gmail.com>
 * @copyright 2008 Sergey S. Sergeev
 * @copyright 2014 Eric I. Gorbikov <ernest.gorbikov@gmail.com>
 */
class FilterFactory extends StaticFactory
{
	/**
     * @return HtmlEntitiesEncode
     */
    public static function textEncode()
    {
		return Singleton::getInstance('HtmlEntitiesEncode');
    }

    /**
     * @return HtmlEntitiesDecode
     */
    public static function textDecode()
    {
		return Singleton::getInstance('HtmlEntitiesDecode');
    }

    /**
     * @return SmartHtmlEncode
     */
    public static function htmlEncode()
    {
		return Singleton::getInstance('SmartHtmlEncode');
    }

    /**
     * @return FilterManyToOneNL
     */
    public static function manyToOneNL()
    {
		return Singleton::getInstance('FilterManyToOneNL');
    }

    /**
     * @return FilterChain
     */
    public static function textImport()
    {
        return
			FilterChain::create()->
				add(Filter::stripTags())->
				add(self::textDecode())->
				add(self::textEncode())->
				add(Filter::safeUtf8())->
				add(self::manyToOneNL())->
				add(Filter::trim())
	        ;
    }


    /**
     * @return FilterChain
     */
    public static function htmlImportFE()
    {
        return
			FilterChain::create()->
				add(Filter::stripTags()->setAllowableTags('<b><i><u><s><strong><p><br><strike><em><font><span><img><a>'))->
				add(FilterFactory::stripSlashes())->
				add(Filter::safeUtf8())->
				add(self::manyToOneNL())->
				add(Filter::trim())
	        ;
    }


    /**
     * @return FilterChain
     */
    public static function htmlDisplay()
    {
        return
			FilterChain::create()->
				add(self::htmlEncode())->
				add(self::textEncode())->
				add(Filter::safeUtf8())->
				add(Filter::trim())
	    ;
    }


    /**
     * @return FilterChain
     */
    public static function htmlImport()
    {
        return
			FilterChain::create()->
				add(self::htmlEncode())->
				add(self::textEncode())->
				add(Filter::safeUtf8())->
				add(Filter::trim());
    }


    /**
     * @return FilterCutOffText
     */
    public static function cutOffText($length)
    {
		return Singleton::getInstance('FilterCutOffText')->setLength((int)$length);
    }


    /**
     * @return FilterIconv
     */
    public static function filterIconv()
    {
		return Singleton::getInstance('FilterIconv');
    }


    /**
     * @return FilterStripSlashes
     */
    public static function stripSlashes()
    {
		return Singleton::getInstance('FilterStripSlashes');
    }
}
<?php
/**
 * @author Eric I. Gorbikov <ernest.gorbikov@gmail.com>
 * @copyright 2014 Eric I. Gorbikov <ernest.gorbikov@gmail.com>
 */

class TemplateUtils extends StaticFactory {

	/**
	 * @return MultiPrefixPhpViewResolver
	 */
	public static function makeDefaultViewResolver()
	{
		$viewResolver = MultiPrefixPhpViewResolver::create();
		$viewResolver->setViewClassName('ExtendedPhpView');
		$viewResolver->dropPrefixes();
		$viewResolver->addPrefix(DIR_TEMPLATES_TINY);
		$viewResolver->addPrefix(DIR_TEMPLATES . 'controllers' . DS);
		$viewResolver->addPrefix(DIR_TEMPLATES . 'parts' . DS);
		$viewResolver->addPrefix(DIR_TEMPLATES);

		return $viewResolver;
	}
}
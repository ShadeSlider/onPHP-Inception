<?php
/**
 * @author Eric I. Gorbikov <ernest.gorbikov@gmail.com>
 * @copyright 2014 Eric I. Gorbikov <ernest.gorbikov@gmail.com>
 */
define('DIR_CLASSES_INCEPTION', DIR_CLASSES.'Inception'.DS);
AutoloaderPool::get('onPHP')->
	addPaths(array(
		DIR_CLASSES_INCEPTION.'Core'.DS,
		DIR_CLASSES_INCEPTION.'Flow'.DS,
		DIR_CLASSES_INCEPTION.'Flow'.DS.'Filters'.DS,
		DIR_CLASSES_INCEPTION.'Flow'.DS.'Controllers'.DS,
		DIR_CLASSES_INCEPTION.'Flow'.DS.'Feed'.DS,
		DIR_CLASSES_INCEPTION.'Misc'.DS,
		DIR_CLASSES_INCEPTION.'UI'.DS,
		DIR_CLASSES_INCEPTION.'Utils'.DS,
		DIR_CLASSES_INCEPTION.'Utils'.DS.'Logging'.DS,

		DIR_CLASSES_INCEPTION.DS
	));

require DIR_CLASSES_INCEPTION.'Utils/functionSet.php';
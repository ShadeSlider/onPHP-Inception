<?php
/**
 * @author Eric I. Gorbikov <ernest.gorbikov@gmail.com>
 * @copyright 2014 Eric I. Gorbikov <ernest.gorbikov@gmail.com>
 */

class MetaDataManager {


	/** @var  SimpleXMLElement */
	protected $xml;

	function __construct($filePath)
	{
		if(is_dir($filePath)) {
			$this->loadXMLFromDirectory($filePath);
		}
		else {
			$this->loadXMLFromFile($filePath);
		}
	}

	/**
	 * @return static
	 */
	public static function create($filePath)
	{
		return new static($filePath);
	}


	/**
	 * @return static
	 * @throws IOException
	 */
	protected function loadXMLFromDirectory($directoryPath)
	{
		$this->xml = new SimpleXMLElementExtended('<metadata></metadata>');

		foreach(glob($directoryPath . "*.xml") as $filePath) {
			try {
				$fileXml = new SimpleXMLElementExtended(file_get_contents($filePath));


//				vd($fileXml->children());
				/** @var SimpleXMLElementExtended $newXmlSection */
				foreach($fileXml->children() as $newXmlSection) {

					if(!isset($this->xml->{$newXmlSection->getName()})) {
						$xmlSection = $this->xml->addChild($newXmlSection->getName());
					}

					/** @var SimpleXMLElementExtended $newXmlItem */
					foreach($newXmlSection->children() as $newXmlItem) {
						$xmlSection->addXMLChild($newXmlItem);
					}
				}

			} catch(BaseException $e) {
				throw new IOException("Cannot load meta data file '$filePath'. File doesn't exist?");
			}
		}

		return $this;
	}

	
	/**
	 * @return static
	 * @throws IOException
	 */
	protected function loadXMLFromFile($filePath)
	{
		try {
			$this->xml = new SimpleXMLElement(
				file_get_contents($filePath)
			);
		} catch(BaseException $e) {
			throw new IOException("Cannot load meta data file '$filePath'. File doesn't exist?");
		}

		return $this;
	}


	public function getControllerMetaData($controllerName, $outputType = 'array')
	{
		if($controllerName instanceof BaseController) {
			$controllerName = $controllerName->getCleanName();
		}

		$controllerMetaData = $this->xml->xpath("/metadata/controllers/controller[@name='" . $controllerName . "']");

		if(empty($controllerMetaData)) {
			$controllerMetaData = new SimpleXMLElement('<empty></empty>');
		}
		else {
			$controllerMetaData = array_shift($controllerMetaData);
		}

		$out = null;
		switch($outputType) {
			case 'array':
				$out = $this->simpleXMLToArray($controllerMetaData);
				break;
			case 'StdClass':
				$out = $this->simpleXMLToStdObject($controllerMetaData);
		}

		return $out;
	}


	public function getControllerBreadCrumbs($controllerName, $actionName = null)
	{
		$metaData = $this->getControllerMetaData($controllerName, 'array');

		$breadCrumbs = array();

		if(!empty($metaData['breadCrumbs'])) {
			$breadCrumbs = $metaData['breadCrumbs'];
		}


		foreach($breadCrumbs as $idx => $crumb) {
			if(!empty($crumb['route'])) {
				if(!is_array($crumb['route'])) {
					$crumb['route'] = array('name' => $crumb['route']);
				}

				if(!isset($crumb['route']['params'])) {
					$crumb['route']['params'] = array();
				}

				$initialParams = $crumb['route']['params'];
				$params = array();
				foreach($initialParams as $paramData) {
					$params[$paramData['name']] = $paramData['value'];
				}

				$breadCrumbs[$idx]['route']['params'] = $params;
			}
		}

		if($actionName && !empty($metaData['title' . ucfirst($actionName)])) {
			$breadCrumbs[]['title'] = $metaData['title' . ucfirst($actionName)];
		}

		return $breadCrumbs;
	}


	public function toXML()
	{
		return $this->xml->asXML();
	}

	protected function simpleXMLToStdObject(SimpleXMLElement $xml)
	{
		return json_decode(json_encode($xml));
	}

	protected function simpleXMLToArray($xml)
	{
		$array = (array)$xml;

		foreach ($array as $key => $value){
			if(is_object($value) && strpos(get_class($value),"SimpleXML")!==false) {
				if(count((array)$value) == 1) {

					$innerValueArr = (array)$value;
					$innerValue = reset($innerValueArr);


					if(!is_array($innerValue) || count($innerValueArr) > 1) {
						$array[$key] = $this->simpleXMLToArray($value);
						continue;
					}

					$array[$key] = array();
					foreach ($innerValue as $inner) {
						if(is_object($inner) && strpos(get_class($inner),"SimpleXML")!==false) {
							$array[$key][] = $this->simpleXMLToArray($inner);
						}
						else {
							$array[$key][] = $inner;
						}

					}
				}
				else {
					$array[$key] = $this->simpleXMLToArray($value);
				}
			}
		}

		return $array;
	}
}
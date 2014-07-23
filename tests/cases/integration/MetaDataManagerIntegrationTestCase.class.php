<?php
/**
 * @author Eric I. Gorbikov <ernest.gorbikov@gmail.com>
 * @copyright @copyright 2014 Eric I. Gorbikov <ernest.gorbikov@gmail.com>
 */

class MetaDataManagerIntegrationTestCase extends BaseIntegrationTestCase {


	/**
	 * @test
	 * @expectedException IOException
	 */
	public function created_invalidXmlFileName_throwsIOException()
	{
		MetaDataManager::create('/i/dont/exist.xml');
	}


	/**
	 * @test
	 */
	public function created_validXmlFileName_parsesXml()
	{
		$metaDataManager = $this->makeMetaDataManager();
		$xml = new SimpleXMLElement($this->makeXMLFilePath(), 0, true);

		$this->assertXmlStringEqualsXmlString($xml->asXML(), $metaDataManager->toXML());
	}


	/**
	 * @test
	 */
	public function getControllerMetaDataFor_invalidEntityName_returnsEmptyStdObject()
	{
		$metaDataManager = $this->makeMetaDataManager();
		$controllerMetaData = $metaDataManager->getControllerMetaData('IDontExist', 'StdClass');

		$this->assertInstanceOf('StdClass', $controllerMetaData);
		$this->assertCount(0, (array)$controllerMetaData);
	}


	/**
	 * @test
	 */
	public function getControllerMetaDataFor_validEntityName_returnsStdObjectWithMetaData()
	{
		$metaDataManager = $this->makeMetaDataManager();
		$controllerMetaData = $metaDataManager->getControllerMetaData('BackendUser', 'StdClass');

		$this->assertInstanceOf('StdClass', $controllerMetaData);
		$this->assertNotEmpty($controllerMetaData->title);
		$this->assertNotEmpty($controllerMetaData->messageAdded);
		$this->assertNotEmpty($controllerMetaData->messageUpdated);
	}


	/**************************************************************
	 **************** TEST CREATION METHODS & UTILS ***************
	 **************************************************************/
	/**
	 * @return MetaDataManager
	 */
	protected function makeMetaDataManager()
	{
		return MetaDataManager::create($this->makeXMLFilePath());
	}

	/**
	 * @return string
	 */
	protected function makeXMLFilePath()
	{
		return DIR_TESTS_METADATA . 'xml' .DS . 'meta_data.xml';
	}

}

/**************************************************************
 ******************** FAKE CLASSES FOR TESTS ******************
 **************************************************************/


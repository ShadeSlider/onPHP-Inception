<?php
/**
 * @author Eric I. Gorbikov <ernest.gorbikov@gmail.com>
 * @copyright 2014 Eric I. Gorbikov <ernest.gorbikov@gmail.com>
 */

class DBManager {

	/**
	 * @var DBSchema
	 */
	protected $schema = null;

	/**
	 * @var DBPool
	 */
	protected $pool = null;


	function __construct(DBPool $dbPool = null, DBSchema $schema = null, $connectToAllDB = true)
	{
		if(!empty($schema)) {
			$this->schema = $schema;
		}
		else {
			$schemaPath = DIR_CLASSES . 'Auto' . DS . 'schema.php';
			$this->loadDBSchema($schemaPath);
		}

		$this->pool = $dbPool;

		if($connectToAllDB) {
			$this->connectToAllDB();
		}
	}


	/**
	 * @return static
	 */
	public static function create(DBPool $dbPool = null, DBSchema $schema = null, $connectToAllDB = true)
	{
		return new static($dbPool, $schema, $connectToAllDB);
	}


	/**
	 * @param string $path
	 * @return static
	 */
	public function loadDBSchema($path)
	{
		require $path;
		Assert::isTrue(isset($schema));
		Assert::isInstance($schema, 'DBSchema');
		$this->schema = $schema;
		return $this;
	}


	public function dropAndCreateDBTables($silent = true)
	{
		$this->dropDBTables($silent);
		$this->createDBTables($silent);

		return $this;
	}


	/**
	 * @param bool $silent
	 * @throws DatabaseException
	 * @throws MissingElementException
	 * @return static
	 */
	public function dropDBTables($silent = true)
	{
		foreach ($this->pool->getList() as $name => $db) {
			/* @var $db DB */
			foreach ($this->schema->getTableNames() as $name) {
				try {
					$db->queryRaw(
						OSQL::dropTable($name, true)->toDialectString(
							$db->getDialect()
						)
					);
				} catch (DatabaseException $e) {
					if (!$silent)
						throw $e;
				}

				if ($db->hasSequences()) {
					foreach (
						$this->schema->getTableByName($name)->getColumns()
						as $columnName => $column) {
						try {
							if ($column->isAutoincrement())
								$db->queryRaw("DROP SEQUENCE {$name}_id_seq;");
						} catch (DatabaseException $e) {
							if (!$silent)
								throw $e;
						}
					}
				}
			}
		}

		return $this;
	}


	/**
	 * @return static
	 */
	public function createDBTables()
	{
		foreach ($this->pool->getList() as $dbLinkname => $db) {

			/** @var PgSQL $db */
			foreach ($this->schema->getTables() as $tableName => $table) {
				$db->queryRaw($table->toDialectString($db->getDialect()));
			}
		}

		return $this;
	}


	/**
	 * Fill DB with data
	 * @return static
	 */
	public function fillDB()
	{
		return $this;
	}

	
	/**
	 * @param DBTestPool $dbPool
	 * @return static
	 */
	public function setDBPool(DBPool $dbPool)
	{
		$this->pool = $dbPool;
		return $this;
	}

	public function connectToAllDB()
	{
		/** @var DB $db */
		foreach ($this->pool->getList() as $name => $db) {
			if (!$db->isConnected()) {
				$db->connect();
			}
		}
	}
}
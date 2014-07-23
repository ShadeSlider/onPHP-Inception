<?php

class Logger extends BaseLogger
{
    /**
     * @var SplQueue
     */
    protected $loggers;

    /**
     * @var Logger
     */
    protected static $instance;

    /**
     * @var float
     */
    protected $microtime;


	public static function create()
	{
		return new static;
	}


    public function __construct()
    {
        $this->flushLoggers();

        $this->microtime = microtime(true);

        return;
    }


    /**
     * @return Logger
     */
    public function flushLoggers()
    {
        $this->loggers = new SplQueue();

        $this->loggers->setIteratorMode(SplDoublyLinkedList::IT_MODE_FIFO|SplDoublyLinkedList::IT_MODE_KEEP);

        return $this;
    }


	/**
     * @param BaseLogger $logger
     * @return Logger
     */
    public function add(BaseLogger $logger)
    {
        if (!$logger->getLevel()) $logger->setLevel($this->getLevel());

        $this->loggers->push($logger);

        return $this;
    }

    /**
     * @return array BaseLogger[]
     */
    public function getLoggers()
    {
        return $this->loggers;
    }

    /**
     * @param Exception $e
     * @return Logger
     */
    public function exception(Exception $e)
    {
        $message = '['.get_class($e)."] {$e->getMessage()}\n{$e->getFile()}:{$e->getLine()}, code:{$e->getCode()}\n{$e->getTraceAsString()}";

        $this->log(LogLevel::severe(), $message);

        return $this;
    }

    protected /* void */ function publish(LogRecord $record)
    {
	    $loggers = $this->getLoggers();

        $loggers->rewind();


        foreach($loggers as $logger) {
            try {

                $logger->publish(
                    LogRecord::create()
                        ->setDate($record->getDate())
                        ->setLevel($record->getLevel())
                        ->setMessage("[{$this->microtime}] {$record->getMessage()}")
                );

            } catch(BaseException $e) {
                continue;
            }
        }
    }
}
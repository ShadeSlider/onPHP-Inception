<?php

final class GrayLogLogger extends BaseLogger
{
    const ENCODING = 'utf8';

    /**
     * @var IGelfPublisher
     */
    protected $publisher;

    /**
     * @var array
     */
    protected $levels = array(
        LogLevel::SEVERE => GelfMessage::EMERGENCY,
        LogLevel::WARNING => GelfMessage::WARNING,
        LogLevel::INFO => GelfMessage::INFO,
        LogLevel::CONFIG => GelfMessage::INFO,
        LogLevel::FINE => GelfMessage::NOTICE,
        LogLevel::FINER => GelfMessage::DEBUG,
        LogLevel::FINEST => GelfMessage::DEBUG
    );

    public function __construct(IGelfPublisher $publisher)
    {
        $this->setPublisher($publisher);
    }

    /**
     * @return BaseLogger
     */
    public static function create(IGelfPublisher $publisher)
    {
        return new self($publisher);
    }

    /**
     * @return IGelfPublisher
     */
    public function getPublisher()
    {
        return $this->publisher;
    }

    /**
     * @param IGelfPublisher $publisher
     * @return GrayLogLogger
     */
    public function setPublisher(IGelfPublisher $publisher)
    {
        $this->publisher = $publisher;
        return $this;
    }

    protected function publish(LogRecord $record)
    {
        $this->getPublisher()->send($this->assembleMessage($record));
    }

    /**
     * @param LogRecord $record
     * @return IGelfMessage
     */
    protected function assembleMessage(LogRecord $record)
    {
        return
            GraylogMessage::create()
                ->setLevel($this->levels[$record->getLevel()->getId()])
                ->setHost((isset($_SERVER['SERVER_ADDR'])) ? $_SERVER['SERVER_ADDR'] : Application::me()->getHostName())
                ->setShortMessage(
                    (mb_strlen($record->getMessage(), self::ENCODING) > 100)
                        ? mb_substr($record->getMessage(), 0, 100, self::ENCODING).' ...'
                        : $record->getMessage()
                )
                ->setTimestamp($record->getDate()->toFormatString('U'))
                ->setFacility(
                        (Application::me()->getServerType())
                            ? Application::me()->getServerType().'-'.Application::me()->getProjectName()
                            : Application::me()->getProjectName()
                )
                ->setFullMessage($record->getMessage())
            ;
    }
}
<?php

final class FileLogger extends BaseLogger
{
    /**
     * @var string
     */
    protected $logDir;

    public function __construct($logDir)
    {
        if (!file_exists($logDir) || !is_writable($logDir)) {
            throw new ConfigurationException("path is not writable '{$logDir}'");
        }

        $this->logDir = $logDir;
    }

    /**
     * @return BaseLogger
     */
    public static function create($logDir)
    {
        return new self($logDir);
    }

    protected function publish(LogRecord $record)
    {
        $env = Application::me()->getServerType() ?: 'UNKNOWN';

        $date = date('Y-m-d');

        $file = rtrim($this->logDir, DS) . DS . "{$date}_{$env}.log";

        $text = "[{$record->getDate()->toIsoString()}] {$record->getMessage()}";

        if (!file_put_contents($file, $text, FILE_APPEND)) {
            throw new ConfigurationException("unable to write log into file '{$file}'");
        }
    }
}
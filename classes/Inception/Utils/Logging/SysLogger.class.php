<?php

final class SysLogger extends BaseLogger
{
    /**
     * @return BaseLogger
     */
    public static function create()
    {
        return new self();
    }

    protected function publish(LogRecord $record)
    {
        syslog(
            $record->getLevel()->getId(),
            "[{$record->getDate()->toIsoString()}] {$record->getMessage()}"
        );
    }
}
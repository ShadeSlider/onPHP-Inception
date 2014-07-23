<?php

final class ConsoleLogger extends BaseLogger
{
    /**
     * @return ConsoleLogger
     */
    public static function create()
    {
        return new self;
    }

    protected function publish(LogRecord $record)
    {
        return
            file_put_contents(
                "php://stdout",
                "[{$record->getLevel()->getName()}] {$record->getMessage()}\n"
            );
    }
}
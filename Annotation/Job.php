<?php

namespace Padam87\CronBundle\Annotation;

use Attribute;

/**
 * @Annotation
 * @Target("CLASS")
 */
#[Attribute(Attribute::TARGET_CLASS)]
class Job
{
    public string $minute = '*';
    public string $hour = '*';
    public string $day = '*';
    public string $month = '*';
    public string $dayOfWeek = '*';

    public ?string $group = null;

    public ?string $logFile = null;

    public ?string $commandLine = null;

    public function __construct(
        /** @var string|array */ $minute,
        string $hour = null,
        string $day = null,
        string $month = null,
        string $dayOfWeek = null,
        string $group = null,
        string $logFile = null,
        ?string $commandLine = null
    ) {
        if (is_array($minute)) {
            $arguments = $minute;

            $this->commandLine = null;

            foreach ($arguments as $key => $value) {
                $this->{$key} = $value;
            }
        } else {
            $this->commandLine = $commandLine;
            $this->minute = $minute;
            $this->hour = $hour;
            $this->day = $day;
            $this->month = $month;
            $this->dayOfWeek = $dayOfWeek;
            $this->group = $group;
            $this->logFile = $logFile;
        }
    }
}

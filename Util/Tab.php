<?php

namespace Padam87\CronBundle\Util;

use ArrayAccess;
use Padam87\CronBundle\Annotation\Job;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\BufferedOutput;
use UnexpectedValueException;

class Tab implements ArrayAccess
{
    /**
     * @var Job[]
     */
    private array $jobs = [];
    private VariableBag $vars;

    public function __construct()
    {
        $this->vars = new VariableBag();
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset): bool
    {
        return isset($this->jobs[$offset]);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset): Job
    {
        return $this->jobs[$offset];
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value): void
    {
        if (!$value instanceof Job) {
            throw new UnexpectedValueException(
                sprintf(
                    'The crontab should only contain instances of "%s", "%s" given',
                    Job::class,
                    get_class($value)
                )
            );
        }

        if (is_null($offset)) {
            $this->jobs[] = $value;
        } else {
            $this->jobs[$offset] = $value;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset): void
    {
        unset($this->jobs[$offset]);
    }

    public function __toString(): string
    {
        $output = new BufferedOutput();
        $table = new Table($output);
        $table->setStyle('compact');

        foreach ($this->jobs as $job) {
            $table->addRow(
                [
                    str_replace('\/', '/', $job->minute),
                    $job->hour,
                    $job->day,
                    $job->month,
                    $job->dayOfWeek,
                    $job->commandLine . ($job->logFile ? ' >> ' . $job->logFile : ''),
                ]
            );
        }

        $table->render();

        return $this->vars . PHP_EOL . $output->fetch();
    }

    /**
     * @return Job[]
     */
    public function getJobs(): array
    {
        return $this->jobs;
    }

    public function getVars(): VariableBag
    {
        return $this->vars;
    }
}

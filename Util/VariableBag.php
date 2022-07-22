<?php

namespace Padam87\CronBundle\Util;

use ArrayAccess;
use Padam87\CronBundle\Annotation\Job;

class VariableBag implements ArrayAccess
{
    private array $vars = [];

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset): bool
    {
        return isset($this->vars[$offset]);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset): Job
    {
        return $this->vars[$offset];
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value): void
    {
        $this->vars[$offset] = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset): void
    {
        unset($this->vars[$offset]);
    }

    public function __toString(): string
    {
        $string = '';
        foreach ($this->vars as $k => $v) {
            $string .= sprintf('%s=%s', $k, $v) . PHP_EOL;
        }

        return $string;
    }

    public function getVars(): array
    {
        return $this->vars;
    }
}

<?php

namespace Padam87\CronBundle\Util;

use Doctrine\Common\Annotations\AnnotationReader;
use Padam87\CronBundle\Annotation\Job;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionException;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\LazyCommand;
use Symfony\Component\Console\Input\InputInterface;

class Helper
{
    private Application $application;
    private AnnotationReader $annotationReader;

    public function __construct(Application $application, AnnotationReader $annotationReader)
    {
        $this->application = $application;
        $this->annotationReader = $annotationReader;
    }

    /**
     * @throws ReflectionException
     */
    public function createTab(InputInterface $input, ?array $config = null): Tab
    {
        $attributesSupported = class_exists('ReflectionAttribute');

        $group = $input->hasOption('group') ? $input->getOption('group') : null;

        $tab = new Tab();

        foreach ($this->application->all() as $command) {
            $commandInstance = $command instanceof LazyCommand
                ? $command->getCommand()
                : $command;

            $reflectionClass = new ReflectionClass($commandInstance);

            $attributesOrAnnotations = array_merge(
                ...array_filter([
                $this->annotationReader->getClassAnnotations($reflectionClass),
                $attributesSupported?$reflectionClass->getAttributes():null,
            ]));

            if ([] === $attributesOrAnnotations) {
                continue;
            }

            foreach ($attributesOrAnnotations as $attributesOrAnnotation) {
                if ($attributesSupported && $attributesOrAnnotation instanceof ReflectionAttribute
                    && $attributesOrAnnotation->getName() === Job::class) {

                    $attributesOrAnnotation = new Job($attributesOrAnnotation->getArguments());
                }

                if ($attributesOrAnnotation instanceof Job) {
                    if ($group !== null && $group !== $attributesOrAnnotation->group) {
                        continue;
                    }

                    $attributesOrAnnotation->commandLine = sprintf(
                        '%s %s %s',
                        $config['php_binary'],
                        realpath($_SERVER['argv'][0]),
                        $attributesOrAnnotation->commandLine ?? $commandInstance->getName()
                    );

                    if ($config['log_dir'] !== null && $attributesOrAnnotation->logFile !== null) {
                        $logDir = rtrim($config['log_dir'], '\\/');
                        $attributesOrAnnotation->logFile = $logDir.DIRECTORY_SEPARATOR.$attributesOrAnnotation->logFile;
                    }

                    $tab[] = $attributesOrAnnotation;
                }
            }
        }

        $vars = $tab->getVars();

        if ($input->hasOption('env')) {
            $vars['SYMFONY_ENV'] = $input->getOption('env');
        }

        if ($config !== null) {
            foreach ($config['variables'] as $name => $value) {
                if ($value === null) {
                    continue;
                }

                $vars[strtoupper($name)] = $value;
            }
        }

        return $tab;
    }
}

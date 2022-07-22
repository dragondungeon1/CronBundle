<?php

namespace Padam87\CronBundle\Command;

use Exception;
use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class ConfigurationAwareCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this
            ->addOption('group', 'g', InputArgument::OPTIONAL)
        ;
    }

    /**
     * @throws Exception
     */
    public function getConfiguration(): array
    {
        if (method_exists($this->getApplication(), "getKernel")) {
            /** @noinspection PhpUndefinedMethodInspection */
            /** @var ContainerInterface $container */
            $container = $this->getApplication()->getKernel()->getContainer();

            $config = $container->getParameter('padam87_cron');
        } else {
            throw new RuntimeException('Not implemented yet. PRs are welcome, as always.');
        }

        return $config;
    }
}

<?php

namespace Padam87\CronBundle\Command;

use Doctrine\Common\Annotations\AnnotationReader;
use Padam87\CronBundle\Util\Helper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DumpCommand extends ConfigurationAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('cron:dump')
            ->setDescription('Dumps jobs to a crontab file')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);

        $this->dump($input);
    }

    /**
     * @param InputInterface $input
     *
     * @return string
     */
    protected function dump(InputInterface $input)
    {
        $reader = new AnnotationReader();
        $helper = new Helper($this->getApplication(), $reader);

        $tab = $helper->read($input, $input->getOption('group'), $this->getConfiguration());

        $path = strtolower(
            sprintf(
                '%s.crontab',
                $this->getApplication()->getName()
            )
        );
        file_put_contents($path, (string) $tab);

        return $path;
    }
}

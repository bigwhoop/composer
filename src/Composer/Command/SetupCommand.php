<?php

/*
 * This file is part of Composer.
 *
 * (c) Nils Adermann <naderman@naderman.de>
 *     Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Composer\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Composer\Config;
use Composer\Setup\Worker;
use Composer\Setup\StepFactory;

/**
 * @author Philippe Gerber <philippe@bigwhoop.ch>
 */
class SetupCommand extends Command
{
    protected function configure()
    {
        $help = <<<EOT
The <info>setup</info> command reads the composer.json file from the
current directory, processes it, and asks your for further application
configuration/setup.

<info>php composer.phar setup</info>
EOT;

        $this->setName('setup')
             ->setDescription('Starts step-by-step configuration of this application')
             ->setHelp($help);
    }


    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $composer = $this->getComposer();
        $config   = $composer->getConfig();

        $worker = new Worker();

        foreach ((array)$config->get('setup') as $stepData) {
            $stepConfig = new Config($stepData);
            $step = StepFactory::factory($stepConfig);

            $worker->addStep($step);
        }


        $worker->execute($input, $output);
    }
}

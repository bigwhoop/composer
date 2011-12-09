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

namespace Composer\Setup;

use Composer\Config;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Philippe Gerber <philippe@bigwhoop.ch>
 */
class InformationTask implements TaskInterface
{
    /**
     * @var string
     */
    private $text;


    /**
     * @param string $text
     */
    public function __construct($text)
    {
        $this->text = (string)$text;
    }


    /**
     * @param Worker $worker
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    public function execute(Worker $worker, InputInterface $input, OutputInterface $output)
    {
        $text = $this->text;

        foreach ($worker->getVariables() as $key => $value) {
            $text = str_replace("%$key%", $value, $text);
        }

        $output->writeln($text);
    }
}

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

namespace Composer\Setup\Task;

use Composer\Config;
use Composer\Setup\Worker;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Philippe Gerber <philippe@bigwhoop.ch>
 */
class ReplacePlaceholdersTask implements TaskInterface
{
    /**
     * @var string
     */
    private $source;


    /**
     * @param string $source
     */
    public function __construct($source)
    {
        $this->source = (string)$source;
    }


    /**
     * @param Worker $worker
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    public function execute(Worker $worker, InputInterface $input, OutputInterface $output)
    {
        if (!file_exists($this->source)) {
            throw new \RuntimeException("Can't replace variables in non-existing file at '{$this->source}'.");
        }

        if (!is_writable($this->source)) {
            throw new \RuntimeException("Can't write to source file at '{$this->source}'.");
        }

        $contents = file_get_contents($this->source);

        foreach ($worker->getVariables() as $key => $value) {
            $contents = str_replace("%$key%", $value, $contents);
        }

        file_put_contents($this->source, $contents);
    }
}

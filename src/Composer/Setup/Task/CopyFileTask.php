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
class CopyFileTask implements TaskInterface
{
    /**
     * @var string
     */
    private $source;

    /**
     * @var string
     */
    private $destination;


    /**
     * @param string $source
     * @param string $destination
     */
    public function __construct($source, $destination)
    {
        $this->source      = (string)$source;
        $this->destination = (string)$destination;
    }


    /**
     * @param Worker $worker
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    public function execute(Worker $worker, InputInterface $input, OutputInterface $output)
    {
        if (!file_exists($this->source) || !is_readable($this->source)) {
            throw new \RuntimeException("Can't read from file at '{$this->source}'.");
        }

        if ((file_exists($this->destination) && !is_writable($this->destination))
            || (!file_exists($this->destination) && !is_writable(dirname($this->destination)))) {
            throw new \RuntimeException("Can't write to destination file at '{$this->destination}'.");
        }

        copy($this->source, $this->destination);
    }
}

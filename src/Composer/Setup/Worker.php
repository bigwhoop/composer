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
class Worker
{
    /**
     * @var array
     */
    private $tasks = array();

    /**
     * @var array
     */
    private $variables = array();


    /**
     * @param TaskInterface $task
     * @return Worker
     */
    public function addTask(TaskInterface $task)
    {
        $this->tasks[] = $task;
        return $this;
    }


    /**
     * @param string $key
     * @param mixed $value
     * @return Worker
     */
    public function setVariable($key, $value)
    {
        $this->variables[$key] = $value;
        return $this;
    }


    /**
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getVariable($key, $default = null)
    {
        if (array_key_exists($key, $this->variables)) {
            return $this->variables[$key];
        }

        return $default;
    }


    /**
     * @return array
     */
    public function getVariables()
    {
        return $this->variables;
    }


    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @return Worker
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        foreach ($this->tasks as $task) {
            $task->execute($this, $input, $output);
        }

        return $this;
    }
}

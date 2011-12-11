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
use Composer\Setup\Validator\ValidatorFactory;

/**
 * @author Philippe Gerber <philippe@bigwhoop.ch>
 */
class TaskFactory
{
    /**
     * @param Config $config
     * @return TaskInterface
     * @throws \OutOfBoundsException
     */
    static public function factory(Config $config)
    {
        $type = $config->get('type');

        switch ($type)
        {
            case 'question':
                $validators = array();
                foreach ((array)$config->get('validators', array()) as $validatorName) {
                    $validators[] = ValidatorFactory::factory($validatorName);
                }

                return new QuestionTask(
                    $config->get('question'),
                    $config->get('variable'),
                    $validators,
                    $config->get('default')
                );

            case 'info':
                return new InformationTask($config->get('text'));

            case 'copyFile':
                return new CopyFileTask($config->get('source'), $config->get('destination'));

            case 'replacePlaceholders':
                return new ReplacePlaceholdersTask($config->get('source'));

            default : throw new \OutOfBoundsException("Invalid task type '{$type}'.");
        }
    }
}

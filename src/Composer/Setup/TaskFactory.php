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

                return new QuestionTask($config->get('question'), $config->get('variable'), $validators);

            case 'info':
                return new InformationTask($config->get('text'));

            default : throw new \OutOfBoundsException("Invalid task type '{$type}'.");
        }
    }
}

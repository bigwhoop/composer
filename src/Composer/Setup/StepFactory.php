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

/**
 * @author Philippe Gerber <philippe@bigwhoop.ch>
 */
class StepFactory
{
    /**
     * @param Config $config
     * @return StepInterface
     */
    static public function factory(Config $config)
    {
        $type = $config->get('type');

        switch ($type)
        {
            case 'question':
                return new QuestionStep($config->get('question'), $config->get('variable'));

            case 'info':
                return new InformationStep($config->get('text'));

            default : throw new \OutOfBoundsException("Invalid step type '{$type}'.");
        }
    }
}

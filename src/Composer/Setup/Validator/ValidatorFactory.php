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

namespace Composer\Setup\Validator;

/**
 * @author Philippe Gerber <philippe@bigwhoop.ch>
 */
class ValidatorFactory
{
    /**
     * @param string $name
     * @return ValidatorInterface
     */
    static public function factory($name)
    {
        $class = __NAMESPACE__ . '\\' . ucfirst($name) . 'Validator';
        $obj = new $class;

        if (!$obj instanceof ValidatorInterface) {
            throw new \InvalidArgumentException("Class '$class' must implement ValidatorInterface.");
        }

        return $obj;
    }
}

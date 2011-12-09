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
class NotEmptyValidator implements ValidatorInterface
{
    /**
     * @param mixed $value
     * @return bool
     */
    public function isValid($value)
    {
        return !empty($value);
    }
}

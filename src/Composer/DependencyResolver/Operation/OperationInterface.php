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

namespace Composer\DependencyResolver\Operation;

use Composer\Package\PackageInterface;

/**
 * Solver operation interface.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
interface OperationInterface
{
    /**
     * Returns job type.
     *
     * @return  string
     */
    function getJobType();

    /**
     * Returns operation reason.
     *
     * @return  string
     */
    function getReason();

    /**
     * Serializes the operation in a human readable format
     *
     * @return string
     */
    function __toString();
}

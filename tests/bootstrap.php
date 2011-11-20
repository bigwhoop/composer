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

require __DIR__.'/../vendor/.composer/autoload.php';

$loader = new Composer\Autoload\ClassLoader();
$loader->add('Composer\Test', __DIR__);
$loader->register();

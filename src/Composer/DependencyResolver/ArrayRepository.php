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

namespace Composer\DependencyResolver;

use Composer\Package\PackageInterface;

/**
 * A repository implementation that simply stores packages in an array
 *
 * @author Nils Adermann <naderman@naderman.de>
 */
class ArrayRepository implements RepositoryInterface
{
    protected $packages = array();

    /**
     * Adds a new package to the repository
     *
     * @param PackageInterface $package
     */
    public function addPackage(PackageInterface $package)
    {
        $package->setRepository($this);
        $this->packages[] = $package;
    }

    /**
     * Returns all contained packages
     *
     * @return array All packages
     */
    public function getPackages()
    {
        return $this->packages;
    }

    /**
     * Returns the number of packages in this repository
     *
     * @return int Number of packages
     */
    public function count()
    {
        return count($this->packages);
    }
}

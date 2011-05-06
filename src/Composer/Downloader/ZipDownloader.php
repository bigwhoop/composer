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

namespace Composer\Downloader;

use Composer\Package\PackageInterface;

/**
 * @author Jordi Boggiano <j.boggiano@seld.be>
 */
class ZipDownloader
{
    public function download(PackageInterface $package, $path)
    {
        if (!class_exists('ZipArchive')) {
            throw new \UnexpectedValueException('You need the zip extension enabled to use the ZipDownloader');
        }

        $tmpName = tempnam(sys_get_temp_dir(), '');
        $this->downloadFile($package->getSourceUrl(), $tmpName);

        if (!file_exists($tmpName)) {
            throw new \UnexpectedValueException($path.' could not be saved into '.$tmpName.', make sure the'
                .' directory is writable and you have internet connectivity.');
        }

        $zipArchive = new ZipArchive();

        if (true === ($retval = $zipArchive->open($tmpName))) {
            $zipArchive->extractTo($path.'/'.$package->getName());
            $zipArchive->close();
        } else {
            throw new \UnexpectedValueException($tmpName.' is not a valid zip archive, got error code '.$retval);
        }
    }

    protected function downloadFile ($url, $path)
    {
        $file = fopen($url, "rb");
        if ($file) {
            $newf = fopen($path, "wb");
            if ($newf) {
                while (!feof($file)) {
                    fwrite($newf, fread($file, 1024 * 8), 1024 * 8);
                }
            }
        }
        if ($file) {
            fclose($file);
        }
        if ($newf) {
            fclose($newf);
        }
    }
}
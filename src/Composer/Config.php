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

namespace Composer;

use IteratorAggregate;
use ArrayIterator;
use ArrayAccess;

/**
 * @author Philippe Gerber <philippe@bigwhoop.ch>
 */
class Config implements IteratorAggregate, ArrayAccess
{
    /**
     * @var array
     */
    private $data = array();


    /**
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }


    /**
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        $keys = explode('/', $key);
        $key  = array_shift($keys);
        $data = $this->data;

        while (count($keys) > 0 && array_key_exists($key, $data)) {
            if (is_array($data[$key])) {
                $data = $data[$key];
                $key  = array_shift($keys);
            }
        }

        return array_key_exists($key, $data) ? $data[$key] : $default;
    }


    /**
     * @param string $key
     * @return bool
     */
    public function has($key)
    {
        $challenge = uniqid();
        return $this->get($key, $challenge) !== $challenge;
    }


    /**
     * @param string $key
     * @param mixed $value
     * @return Config
     * @throws \InvalidArgumentException
     */
    public function set($key, $value)
    {
        $origKey = $key;

        $keys = explode('/', $key);
        $data = &$this->data;

        while (count($keys) > 1) {
            $key = array_shift($keys);

            if (array_key_exists($key, $data)) {
                if (!is_array($data[$key])) {
                    throw new \InvalidArgumentException("Can't set key '$origKey'. Sub-key already exists and is not an array.");
                }
            } else {
                $data[$key] = array();
            }

            $data = &$data[$key];
        }

        $key = array_shift($keys);
        $data[$key] = $value;

        unset($data); // Clear ref

        return $this;
    }


    /**
     * @param Config $config
     * @return Config
     */
    public function merge(Config $config)
    {
        $this->data = array_merge_recursive($this->data, $config->getData());
        return $this;
    }


    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }


    /**
     * @return ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->data);
    }
    
    /**
     * @param string|int $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->data[] = $value;
        } else {
            $this->data[$offset] = $value;
        }
    }


    /**
     * @param string|int $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return isset($this->data[$offset]);
    }


    /**
     * @param string|int $offset
     */
    public function offsetUnset($offset)
    {
        unset($this->data[$offset]);
    }


    /**
     * @param string|int $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return isset($this->data[$offset]) ? $this->data[$offset] : null;
    }
}

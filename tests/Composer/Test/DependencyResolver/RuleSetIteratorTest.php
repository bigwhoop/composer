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

namespace Composer\Test\DependencyResolver;

use Composer\DependencyResolver\Rule;
use Composer\DependencyResolver\RuleSet;
use Composer\DependencyResolver\RuleSetIterator;

class ResultSetIteratorTest extends \PHPUnit_Framework_TestCase
{
    protected $rules;

    protected function setUp()
    {
        $this->rules = array(
            RuleSet::TYPE_JOB => array(
                new Rule(array(), 'job1', null),
                new Rule(array(), 'job2', null),
            ),
            RuleSet::TYPE_UPDATE => array(
                new Rule(array(), 'update1', null),
            ),
            RuleSet::TYPE_PACKAGE => array(),
        );
    }

    public function testForeach()
    {
        $ruleSetIterator = new RuleSetIterator($this->rules);

        $result = array();
        foreach ($ruleSetIterator as $rule)
        {
            $result[] = $rule;
        }

        $expected = array(
            $this->rules[RuleSet::TYPE_JOB][0],
            $this->rules[RuleSet::TYPE_JOB][1],
            $this->rules[RuleSet::TYPE_UPDATE][0],
        );

        $this->assertEquals($expected, $result);
    }
}

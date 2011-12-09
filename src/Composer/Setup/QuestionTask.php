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
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Composer\Setup\Validator\ValidatorInterface;

/**
 * @author Philippe Gerber <philippe@bigwhoop.ch>
 */
class QuestionTask implements TaskInterface
{
    /**
     * @var string
     */
    private $question;

    /**
     * @var string
     */
    private $variable;

    /**
     * @var array
     */
    private $validators = array();


    /**
     * @param string $question
     * @param string $variable
     * @param array $validators        Array of ValidatorInterface objects
     */
    public function __construct($question, $variable, array $validators = array())
    {
        $this->question = (string)$question;
        $this->variable = (string)$variable;

        foreach ($validators as $validator) {
            $this->addValidator($validator);
        }
    }


    /**
     * @param Validator\ValidatorInterface $validator
     * @return QuestionTask
     */
    private function addValidator(ValidatorInterface $validator)
    {
        $this->validators[] = $validator;
        return $this;
    }


    /**
     * @param Worker $worker
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    public function execute(Worker $worker, InputInterface $input, OutputInterface $output)
    {
        $validators = $this->validators;

        $condition = function($data) use ($validators) {
            foreach ($validators as $validator) {
                if (!$validator->isValid($data)) {
                    return false;
                }
            }

            return true;
        };

        do {
            $output->write($this->question . ' ');
            $data = fgets(STDIN, 1024);
            $data = trim($data);
        }
        while (!$condition($data));

        $worker->setVariable($this->variable, $data);
    }
}

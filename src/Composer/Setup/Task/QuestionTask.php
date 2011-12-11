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

namespace Composer\Setup\Task;

use Composer\Config;
use Composer\Setup\Worker;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Composer\Setup\Validator\ValidatorInterface;
use Composer\Setup\Validator\NotEmptyValidator;

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
     * @var false|string
     */
    private $default = false;


    /**
     * @param string $question
     * @param string $variable
     * @param array $validators        Array of ValidatorInterface objects
     * @param string|false $default
     */
    public function __construct($question, $variable, array $validators = array(), $default = false)
    {
        $this->question = (string)$question;
        $this->variable = (string)$variable;

        foreach ($validators as $validator) {
            $this->addValidator($validator);
        }

        $default = trim($default);
        if (!empty($default)) {
            $this->default = (string)$default;
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

        $question = $this->question . ' ';
        if ($this->default !== false) {
            $question .= "[default: {$this->default}] ";
        }

        do {
            $output->write($question);
            $data = fgets(STDIN, 1024);
            $data = trim($data);
        }
        while (!$condition($data));

        if ($this->default !== false) {
            $notEmptyValidator = new NotEmptyValidator();
            if (!$notEmptyValidator->isValid($data)) {
                $data = $this->default;
            }
        }

        $worker->setVariable($this->variable, $data);
    }
}

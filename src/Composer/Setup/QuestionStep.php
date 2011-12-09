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

/**
 * @author Philippe Gerber <philippe@bigwhoop.ch>
 */
class QuestionStep implements StepInterface
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
     * @param string $question
     * @param string $variable
     */
    public function __construct($question, $variable)
    {
        $this->question = (string)$question;
        $this->variable = (string)$variable;
    }


    /**
     * @param Worker $worker
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    public function execute(Worker $worker, InputInterface $input, OutputInterface $output)
    {
        $output->write($this->question . ' ');

        $output->writeln('');
        $worker->setVariable($this->variable, 'Phil');
    }
}

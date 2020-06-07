<?php


namespace Crowbar;


use Symfony\Component\Console\Output\OutputInterface;

/**
 * Easy non-patternfix for output, as there is no sense in using a DI-container in such a smal project for PHAR
 *
 * Class Output
 * @package Crowbar\Ast
 */
class OutputManager
{
    /**
     * @var self
     */
    private static $self;

    /**
     * @var \Symfony\Component\Console\Output\OutputInterface
     */
    private $output;

    /**
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @return OutputManager
     */
    public static function create(\Symfony\Component\Console\Output\OutputInterface $output): self
    {
        self::$self = new self($output);
        return self::$self;
    }

    /**
     *
     */
    public static function getInstance()
    {
        if(self::$self === null){
            throw new \Exception("You ne to create the instance first by calling 'create(\$output)' with a symfony console output-instance!");
        }

        return self::$self;
    }

    /**
     * Output constructor.
     * @param \Symfony\Component\Console\Output\Output $output
     */
    public function __construct(\Symfony\Component\Console\Output\OutputInterface $output)
    {
        $this->output = $output;
    }

    /**
     * @return \Symfony\Component\Console\Output\Output
     */
    public function getOutput(): \Symfony\Component\Console\Output\OutputInterface
    {
        return $this->output;
    }
}
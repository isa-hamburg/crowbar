<?php


namespace Crowbar;


/**
 * Class Config
 * @package Crowbar
 */
class Config
{
    /**
     * @var array
     */
    private $localesToKeep = [];

    /**
     * @var array
     */
    private $filesToKeep = [];

    /**
     * @var string
     */
    private $inputPath;

    /**
     * Config constructor.
     * @param array $config
     */
    public function __construct(array $config)
    {
        foreach($config as $key => $value){
            if(property_exists($this, $key)){
                $this->{$key} = $value;
            }
        }
    }

    /**
     * @return array
     */
    public function getLocalesToKeep(): array
    {
        return $this->localesToKeep;
    }

    /**
     * @return string
     */
    public function getInputPath(): string
    {
        return $this->inputPath;
    }

    /**
     * @return array
     */
    public function getFilesToKeep(): array
    {
        return $this->filesToKeep;
    }
}
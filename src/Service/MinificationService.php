<?php


namespace Crowbar\Service;


use Crowbar\Config;
use Crowbar\Filesystem\SizeComputer;
use Crowbar\OutputManager;
use PhpParser\NodeTraverserInterface;
use PhpParser\Parser;
use PhpParser\PrettyPrinter\Standard;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

/**
 * Class MinificationService
 * @package Crowbar\Service
 */
class MinificationService
{
    /**
     * @var Parser
     */
    private $parser;

    /**
     * @var Standard
     */
    private $standard;

    /**
     * @var NodeTraverserInterface
     */
    private $nodeTraverser;

    /**
     * @var null|string
     */
    private $rootDir = null;

    /**
     * @var SizeComputer
     */
    private $sizeComputer;

    private $filePatternsToRemove = [
        "*.otf",
        "*.ttf",
        "composer.json",
        "composer.lock",
        "*.neon",
        "*.dist",
        "*.md",
        "GPL.txt",
        "LICENCE.txt",
        "LICENSE.txt",
        "Readme",
        "Readme.txt",
        "Readme-TEX.txt",
        "ChangeLog.txt",
        "Bugs",
        "LICENCE",
        ".gitattributes",
        ".gitignore",
        "travis.yml",
        "travis.yaml",
        "Makefile",
        ".editorconfig",
        "README.rst",
        "psalm.xml",
        "docker-compose.yaml",
        "docker-compose.yml",
        ".gitmodules",
        "Dockerfile",
        "puli.json",
        "LICENSE",
        "AUTHORS",
        ".styleci.yml",
        ".styleco.yaml",
        "appveyor.yml",
        "appveyor.yaml",
        ".pullapprove.yml",
        ".pullapprove.yaml",
        "COPYING.txt",
        "LICENSE_AFL.txt",
        "cssmin",
        "phpunit.xml",
        ".scrutinizer.yml",
        ".scrutinizer.yaml",
        ".gitattributes"
    ];

    /**
     * MinificationService constructor.
     * @param Parser $parser
     * @param Standard $standard
     * @param NodeTraverserInterface $nodeTraverser
     * @param SizeComputer $sizeComputer
     */
    public function __construct(Parser $parser, Standard $standard, NodeTraverserInterface $nodeTraverser, SizeComputer $sizeComputer)
    {
        $this->parser = $parser;
        $this->standard = $standard;
        $this->nodeTraverser = $nodeTraverser;
        $this->sizeComputer = $sizeComputer;
    }

    /**
     * @param Config $config
     * @throws \Exception
     */
    public function minifyDirectory(Config $config): void
    {
        $this->rootDir = $config->getInputPath();

        OutputManager::getInstance()->getOutput()->writeln("Removing TrashFolders...");

        $this->removeTrashFolders($config);

        OutputManager::getInstance()->getOutput()->writeln("Success!");

        $this->printDirectorySize();

        OutputManager::getInstance()->getOutput()->writeln("Removing TrashFile...");

        $this->removeTrashFiles($config);

        OutputManager::getInstance()->getOutput()->writeln("Success!");

        $this->printDirectorySize();

        OutputManager::getInstance()->getOutput()->writeln("Minifying PHP Files...");

        $this->minifyFiles($config);

        $this->printDirectorySize();
    }

    /**
     * @param string $inputFile
     * @throws \Exception
     */
    private function minifyFile(string $inputFile): void
    {
        $code = file_get_contents($inputFile);

        $ast = $this->parser->parse($code);

        $ast = $this->nodeTraverser->traverse($ast);

        $code = $this->standard->prettyPrintFile($ast);

        file_put_contents($inputFile, $code);
    }

    /**
     *
     */
    private function removeTrashFolders(Config $config): void
    {
        /**
         * @var Finder $finder
         */
        $finder = (new Finder())->in($this->rootDir)->directories()->name("Test")->name("test")->name("tst")->name("dev")->name("Tests")->name("tests")->name("docs");

        $fileystem = new Filesystem();

        $dirs = iterator_to_array($finder);

        foreach ($dirs as $fileInfo) {
            foreach($config->getFilesToKeep() as $fileToKeep){
                if(strpos($fileInfo->getPathname(), $fileToKeep) !== false){
                    continue;
                }
            }

            try {
                $fileystem->remove($fileInfo->getPathname());
                OutputManager::getInstance()->getOutput()->writeln("Deleting folder '{$fileInfo->getPathname()}'!");
            }catch (\Throwable $exception){
            }
        }
    }

    /**
     *
     */
    private function removeTrashFiles(Config $config)
    {
        /**
         * @var Finder $finder
         */
        $finder = (new Finder())->in($this->rootDir)->files();

        foreach($this->filePatternsToRemove as $item){
            $finder->name($item);
        }

        $filesystem = new Filesystem();

        $files = iterator_to_array($finder);

        foreach ($files as $fileInfo) {
            foreach($config->getFilesToKeep() as $fileToKeep){
                if(strpos($fileInfo->getPathname(), $fileToKeep) !== false){
                    continue;
                }
            }

            try {
                $filesystem->remove($fileInfo->getPathname());
                OutputManager::getInstance()->getOutput()->writeln("Deleting trash-file '{$fileInfo->getPathname()}'!");
            }catch (\Throwable $exception){
            }
        }


        /**
         * @var Finder $finder
         */
        $finder = (new Finder())->in($this->rootDir)->files()->path("Zend/Locale/Data")->name("*.xml");

        $files = iterator_to_array($finder);

        foreach($files as $fileInfo){
            foreach($config->getFilesToKeep() as $fileToKeep){
                if(strpos(strtolower($fileInfo->getPathname()), strtolower($fileToKeep)) !== false){
                    continue;
                }
            }

            $fileName = $fileInfo->getFilenameWithoutExtension();

            if(!in_array(strtolower($fileName), array_map("strtolower", $config->getLocalesToKeep()))){
                $filesystem->remove($fileInfo->getPathname());
                OutputManager::getInstance()->getOutput()->writeln("Deleting trash-file '{$fileInfo->getPathname()}'!");
            }
        }
    }

    /**
     * @throws \Exception
     */
    private function printDirectorySize()
    {
        OutputManager::getInstance()->getOutput()->writeln('Directory: ' . $this->rootDir . ' => Size: ' . $this->sizeComputer->getFormattedFolderSize($this->rootDir) . "\n");
    }

    /**
     * @param Config $config
     * @throws \Exception
     */
    protected function minifyFiles(Config $config): void
    {
        /**
         * @var Finder $finder
         */
        $finder = (new Finder())->in($config->getInputPath())->name("*.php");

        foreach ($finder as $fileInfo) {
            OutputManager::getInstance()->getOutput()->writeln("Minifying file '{$fileInfo->getPathname()}'!");
            $this->minifyFile($fileInfo->getPathname());
        }

        $this->printDirectorySize();
    }
}
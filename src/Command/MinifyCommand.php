<?php


namespace Crowbar\Command;


use Crowbar\Config;
use Crowbar\OutputManager;
use Crowbar\Service\MinificationService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class MinifyCommand
 * @package Crowbar\Command
 */
class MinifyCommand extends Command
{
    /**
     * @var MinificationService
     */
    private $minificationService;

    /**
     * MinifyCommand constructor.
     * @param MinificationService $minificationService
     */
    public function __construct(MinificationService $minificationService)
    {
        $this->minificationService = $minificationService;
        parent::__construct("crowbar:minify");
    }

    /**
     *
     */
    public function configure()
    {
        $this->addArgument("inputPath", InputArgument::REQUIRED);
        $this->addOption("locales", "l", InputOption::VALUE_OPTIONAL, "The locales you want to keep in magento. The more you keep, the bigger it gets...", "de");
        $this->addOption("files", "f", InputOption::VALUE_OPTIONAL, "The files you want to keep. The more you keep, the bigger it gets...");
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        OutputManager::create($output);

        $config = new Config([
            "localesToKeep" => array_filter(array_map("trim", explode(",", $input->getOption("locales")))),
            "inputPath" => realpath($input->getArgument("inputPath")),
            "filesToKeep" => array_filter(array_map("trim", explode(",", $input->getOption("files")))),
        ]);

        try {
            $this->minificationService->minifyDirectory(
                $config
            );
        }catch (\Throwable $exception){
            $output->writeln($exception->getMessage());
            $output->writeln($exception->getTraceAsString());
            return 1;
        }

        return 0;
    }
}
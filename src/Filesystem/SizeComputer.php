<?php


namespace Crowbar\Filesystem;

use Crowbar\Filesystem\SizeComputer\LinuxAdapter;
use Crowbar\Filesystem\SizeComputer\WinAdapter;

/**
 * Class SizeComputer
 * @package Crowbar\Filesystem
 */
class SizeComputer
{
    /**
     * @var WinAdapter
     */
    private $winAdapter;

    /**
     * @var LinuxAdapter
     */
    private $linuxAdapter;

    /**
     * SizeComputer constructor.
     * @param WinAdapter $winAdapter
     * @param LinuxAdapter $linuxAdapter
     */
    public function __construct(WinAdapter $winAdapter, LinuxAdapter $linuxAdapter)
    {
        $this->winAdapter = $winAdapter;
        $this->linuxAdapter = $linuxAdapter;
    }

    /**
     * @param string $folder
     * @return int
     */
    public function getFolderSize(string $folder): int
    {
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            return $this->winAdapter->getFolderSize($folder);
        } else {
            return $this->linuxAdapter->getFolderSize($folder);
        }
    }

    /**
     * @param string $folder
     * @return string
     */
    public function getFormattedFolderSize(string $folder): string
    {
        return $this->formatBytes($this->getFolderSize($folder));
    }

    /**
     * @param $size
     * @param int $precision
     * @return string
     */
    private function formatBytes($size, $precision = 2)
    {
        $base = log($size, 1024);
        $suffixes = array('', 'K', 'M', 'G', 'T');

        return round(pow(1024, $base - floor($base)), $precision) .' '. $suffixes[floor($base)];
    }
}
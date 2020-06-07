<?php


namespace Crowbar\Filesystem\SizeComputer;


/**
 * Class LinuxAdapter
 * @package Crowbar\Filesystem\SizeComputer
 */
class LinuxAdapter implements OsAdapterInterface
{
    /**
     * @param string $folder
     * @return int
     */
    public function getFolderSize(string $folder): int
    {
        $io = popen ( '/usr/bin/du -sk ' . $folder, 'r' );
        $size = fgets ( $io, 4096);
        $size = substr ( $size, 0, strpos ( $size, "\t" ) );
        pclose ( $io );

        return $size;
    }
}
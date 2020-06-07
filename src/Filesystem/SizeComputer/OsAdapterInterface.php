<?php


namespace Crowbar\Filesystem\SizeComputer;


/**
 * Interface OsAdapterInterface
 * @package Crowbar\Filesystem\SizeComputer
 */
interface OsAdapterInterface
{
    /**
     * @param string $folder
     * @return int
     */
    public function getFolderSize(string $folder): int;
}
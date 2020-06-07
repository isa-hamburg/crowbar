<?php


namespace Crowbar\Filesystem\SizeComputer;


use COM;

class WinAdapter implements OsAdapterInterface
{
    /**
     * @param string $folder
     * @return int
     */
    public function getFolderSize(string $folder): int
    {
        $obj = new COM ( 'scripting.filesystemobject' );
        $ref = $obj->getfolder ( $folder);
        return $ref->size;
    }
}
<?php

namespace Sim\File\Filters;

use Sim\File\Interfaces\IFileSystem;
use Sim\File\Interfaces\IFilter;
use SplFileInfo;

class TypeFilter implements IFilter
{
    /**
     * @var int $type
     */
    protected $type;

    /**
     * Type constructor.
     * @param int $type
     */
    public function __construct(int $type = IFileSystem::TYPE_FILE | IFileSystem::TYPE_DIRECTORY)
    {
        $this->type = $type;
    }

    /**
     * {@inheritdoc}
     */
    public function filter(SplFileInfo $file): bool
    {
        $isValid = false;
        $wantDir = $this->type & IFileSystem::TYPE_DIRECTORY;
        $wantFile = $this->type & IFileSystem::TYPE_FILE;
        if (($wantDir && $wantFile) || ($wantDir && $file->isDir()) || ($wantFile && $file->isFile())) {
            $isValid = true;
        }

        return $isValid;
    }
}
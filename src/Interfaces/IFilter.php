<?php

namespace Sim\File\Interfaces;

use SplFileInfo;

interface IFilter
{
    /**
     * @param SplFileInfo $file
     * @return bool
     */
    public function filter(SplFileInfo $file): bool;
}
<?php

namespace Sim\File\Filters;

use Sim\File\Interfaces\IFilter;
use SplFileInfo;

class NameFilter implements IFilter
{
    /**
     * @var string $regex
     */
    protected $regex;

    /**
     * Name constructor.
     * @param string $regex
     */
    public function __construct(string $regex)
    {
        $this->regex = $regex;
    }

    /**
     * {@inheritdoc}
     */
    public function filter(SplFileInfo $file): bool
    {
        $filename = pathinfo($file->getPathname(), PATHINFO_FILENAME);
        return (bool)preg_match($this->regex, (string)$filename);
    }
}
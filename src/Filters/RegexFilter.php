<?php

namespace Sim\File\Filters;

use Sim\File\Interfaces\IFilter;
use SplFileInfo;

class RegexFilter implements IFilter
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
        $filename = $file->getFilename();
        return (bool)preg_match($this->regex, (string)$filename);
    }
}
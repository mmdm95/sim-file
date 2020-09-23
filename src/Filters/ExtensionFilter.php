<?php

namespace Sim\File\Filters;

use InvalidArgumentException;
use Sim\File\Interfaces\IFilter;
use SplFileInfo;

class ExtensionFilter implements IFilter
{
    /**
     * @var array $extensions
     */
    protected $extensions = [];

    /**
     * Extension constructor.
     * @param array $extensions
     */
    public function __construct(array $extensions)
    {
        if (empty($extensions)) {
            throw new InvalidArgumentException('Extensions array is empty!');
        }

        $this->extensions = array_map('strtolower', $extensions);
    }

    /**
     * {@inheritdoc}
     */
    public function filter(SplFileInfo $file): bool
    {
        $extension = strtolower($file->getExtension());
        return in_array($extension, $this->extensions);
    }
}
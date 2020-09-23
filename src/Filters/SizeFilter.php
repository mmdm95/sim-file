<?php

namespace Sim\File\Filters;

use InvalidArgumentException;
use Sim\File\FileSystem;
use Sim\File\Interfaces\IFilter;
use Sim\File\Utils\SizeUtil;
use SplFileInfo;

class SizeFilter implements IFilter
{
    /**
     * @var int $min_size
     */
    protected $min_size = 0;

    /**
     * @var int $max_size
     */
    protected $max_size = PHP_INT_MAX;

    /**
     * $min_size and $max_size should be string like 2MB or 2mb
     * and must be one of ['B', 'KB', 'MB', 'GB', 'TB', 'PB']
     * values, or just pass a number as [B]/bytes or null if
     * there is no need of that.
     *
     * Note:
     *   You should specify at least one of the min or max size
     *   or an InvalidArgumentException will throw.
     *
     * Size constructor.
     * @param string|null $max_size
     * @param string|null $min_size
     */
    public function __construct(?string $max_size, ?string $min_size = null)
    {
        if (empty($min_size) && empty($max_size)) {
            throw new InvalidArgumentException('One of min or max size must be specify.');
        }

        // get min size
        $min_size = !empty($min_size) ? SizeUtil::convertToBytes($min_size, 0) : 0;
        $this->min_size = max($min_size, 0);

        // get max size
        $max_size = !empty($max_size) ? SizeUtil::convertToBytes($max_size, PHP_INT_MAX) : PHP_INT_MAX;
        $this->max_size = min($max_size, PHP_INT_MAX);

        // check which one is greater
        if ($max_size < $min_size) {
            $this->min_size = $max_size;
            $this->max_size = $min_size;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function filter(SplFileInfo $file): bool
    {
        $isValid = true;
        $size = FileSystem::getFileSize($file->getPathname());

        // check for max size and min size
        if ($size > $this->max_size || $size < $this->min_size) {
            $isValid = false;
        }

        return $isValid;
    }
}
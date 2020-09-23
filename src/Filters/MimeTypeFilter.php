<?php

namespace Sim\File\Filters;

use InvalidArgumentException;
use Sim\File\Interfaces\IFilter;
use Sim\File\Utils\MimeTypeUtil;
use SplFileInfo;

class MimeTypeFilter implements IFilter
{
    /**
     * @var array $extensions
     */
    protected $mime_types = [];

    /**
     * MimeType constructor.
     * @param array $mime_types
     */
    public function __construct(array $mime_types)
    {
        if (empty($mime_types)) {
            throw new InvalidArgumentException('Mimetype array is empty!');
        }

        $this->mime_types = $mime_types;
    }

    /**
     * {@inheritdoc}
     */
    public function filter(SplFileInfo $file): bool
    {
        return in_array(MimeTypeUtil::getMimeTypeFromFilename($file->getPathname()), $this->mime_types);
    }
}
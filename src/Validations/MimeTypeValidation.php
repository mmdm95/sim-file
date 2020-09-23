<?php

namespace Sim\File\Validations;

use InvalidArgumentException;
use Sim\File\Abstracts\AbstractValidator;
use Sim\File\FileUpload;
use Sim\File\Utils\MimeTypeUtil;

class MimeTypeValidation extends AbstractValidator
{
    /**
     * {@inheritdoc}
     */
    protected $messages = [
        'mimetype' => 'Specified mimetype is not allowed!',
    ];

    /**
     * @var array $allowed_mime_types
     */
    protected $allowed_mime_types = [];

    /**
     * MimeType constructor.
     * @param array $allowed_mime_types
     */
    public function __construct(array $allowed_mime_types)
    {
        if (empty($allowed_mime_types)) {
            throw new InvalidArgumentException('Allowed mimetypes are not specified.');
        }

        $this->allowed_mime_types = $allowed_mime_types;
    }

    /**
     * {@inheritdoc}
     */
    public function validate(FileUpload $file): bool
    {
        $isValid = in_array(MimeTypeUtil::getMimeTypeFromFilename($file->getPathname()), $this->allowed_mime_types);

        // set error if it is not valid
        if (!$isValid) {
            $this->setError('mimetype', $this->getMessage('mimetype'));
        }

        return $isValid;
    }
}
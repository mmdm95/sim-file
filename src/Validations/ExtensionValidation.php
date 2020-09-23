<?php

namespace Sim\File\Validations;

use InvalidArgumentException;
use Sim\File\Abstracts\AbstractValidator;
use Sim\File\FileUpload;

class ExtensionValidation extends AbstractValidator
{
    /**
     * {@inheritdoc}
     */
    protected $messages = [
        'extension' => 'Specified extension is not allowed!',
    ];

    /**
     * @var array $allowed_extensions
     */
    protected $allowed_extensions = [];

    /**
     * Extension constructor.
     * @param array $allowed_extensions
     */
    public function __construct(array $allowed_extensions)
    {
        if (empty($allowed_extensions)) {
            throw new InvalidArgumentException('Allowed extensions are not specified.');
        }

        $this->allowed_extensions = array_map('strtolower', $allowed_extensions);
    }

    /**
     * {@inheritdoc}
     */
    public function validate(FileUpload $file): bool
    {
        $extension = strtolower($file->getExtension());
        $isValid = in_array($extension, $this->allowed_extensions);

        // set error if it is not valid
        if (!$isValid) {
            $this->setError('extension', $this->getMessage('extension'));
        }

        return $isValid;
    }
}
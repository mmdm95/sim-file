<?php

namespace Sim\File;

use InvalidArgumentException;
use Sim\File\Interfaces\IValidator;
use SplFileInfo;

class FileUpload extends SplFileInfo
{
    /**
     * @var array $validations
     */
    protected $validations = [];

    /**
     * @var array $errors
     */
    protected $errors = [];

    /**
     * @var string $original_name
     */
    protected $original_name;

    /**
     * @var int $error_code
     */
    protected $error_code;

    /**
     * @var array $error_code_messages
     */
    protected $error_code_messages = [
        1 => 'The uploaded file exceeds the upload_max_filesize directive in php.ini',
        2 => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form',
        3 => 'The uploaded file was only partially uploaded',
        4 => 'No file was uploaded',
        6 => 'Missing a temporary folder',
        7 => 'Failed to write file to disk',
        8 => 'A PHP extension stopped the file upload'
    ];

    /**
     * @var array $error_code_messages_translate
     */
    protected $error_code_messages_translate = [];

    /**
     * FileUpload constructor.
     * @param string $key
     */
    public function __construct(string $key)
    {
        if (!isset($_FILES[$key])) {
            throw new InvalidArgumentException("Cannot find uploaded file [{$key}]");
        }

        $this->original_name = $_FILES[$key]['name'];
        $this->error_code = $_FILES[$key]['error'];
        parent::__construct($_FILES[$key]['tmp_name']);
    }

    /**
     * @param array $validations
     * @return static
     */
    public function setValidations(array $validations)
    {
        foreach ($validations as $validation) {
            if ($validation instanceof IValidator) {
                $this->validations[] = $validation;
            }
        }
        return $this;
    }

    /**
     * @return array
     */
    public function getValidations(): array
    {
        return $this->validations;
    }

    /**
     * @return string
     */
    public function getExtension(): string
    {
        return parent::getExtension();
    }

    /**
     * @return int
     */
    public function getSize(): int
    {
        return parent::getSize();
    }

    /**
     * @return string
     */
    public function getOriginalName(): string
    {
        // remove extension
        $name = explode('.', $this->original_name);
        array_pop($name);
        $name = implode('.', $name);

        return $name;
    }

    /**
     * @return string
     */
    public function getOriginalNameWithExtension(): string
    {
        return $this->original_name;
    }

    /**
     * @return array
     */
    public function getErrors(): array
    {
        return array_unique(array_merge($this->errors, $this->error_code_messages_translate));
    }

    /**
     * @param string $error
     * @return static
     */
    public function addError(string $error)
    {
        $this->errors[] = $error;
        return $this;
    }

    /**
     * @param string|int $key
     * @param string $error
     * @return static
     */
    public function setError($key, string $error)
    {
        if (is_string($key) || is_numeric($key)) {
            $this->errors[$key] = $error;
        }
        return $this;
    }

    /**
     * @return bool
     */
    public function hasError(): bool
    {
        return !empty($this->getErrors());
    }

    /**
     * <em>1</em> through <em>8</em> is index of $_FILE error codes
     *
     * @param array $translate
     * @return static
     */
    public function errorMessagesTranslate(array $translate)
    {
        $this->error_code_messages_translate = $translate;
        return $this;
    }
}
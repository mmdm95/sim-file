<?php

namespace Sim\File;

use Sim\File\Interfaces\IUpload;
use Sim\File\Interfaces\IValidator;

class Upload implements IUpload
{
    /**
     * @var FileUpload $file
     */
    protected $file;

    /**
     * @var string $new_name
     */
    protected $new_name;

    /**
     * @var array $errors
     */
    protected $errors = [];

    /**
     * Upload constructor.
     * @param FileUpload $file
     * @param string|null $new_name
     */
    public function __construct(FileUpload $file, ?string $new_name = null)
    {
        $this->file = $file;
        if (!is_null($new_name)) {
            $this->setName($new_name);
        } else {
            $this->setName($file->getOriginalName());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function upload(string $destination, bool $overwrite = false): bool
    {
        if (!$this->validate()) {
            return false;
        }

        if (!$overwrite && file_exists($this->getNameWithExtension())) {
            $this->setError('exist', "File with name {$this->getName()} is already exists!");
            return false;
        }

        return $this->moveUploadedFile($destination);
    }

    /**
     * {@inheritdoc}
     */
    public function setName(string $name)
    {
        $this->new_name = $name;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return $this->new_name;
    }

    /**
     * {@inheritdoc}
     */
    public function getNameWithExtension(): string
    {
        $name = $this->getName();
        $name = strpos($name, '.') ? $name : $name . '.' . $this->file->getExtension();
        return $name;
    }

    /**
     * {@inheritdoc}
     */
    public function getErrors(): array
    {
        return array_unique($this->errors);
    }

    /**
     * {@inheritdoc}
     */
    public function addError(string $error)
    {
        if (!empty(trim($error))) {
            $this->errors[] = $error;
        }
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setError(string $key, string $error)
    {
        if (!empty(trim($error))) {
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
     * @return bool
     */
    protected function validate(): bool
    {
        $validations = $this->file->getValidations();

        /**
         * @var IValidator $validation
         */
        foreach ($validations as $validation) {
            if ($validation->validate($this->file)) {
                $this->errors = array_merge($this->getErrors(), $validation->getErrors());
            }
        }

        return empty($this->getErrors());
    }

    /**
     * @param string $destination
     * @return bool
     */
    protected function moveUploadedFile(string $destination): bool
    {
        return move_uploaded_file($this->file->getPathname(), $destination);
    }
}
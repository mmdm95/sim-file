<?php

namespace Sim\File\Interfaces;

use Sim\File\FileUpload;

interface IValidator
{
    /**
     * @param FileUpload $file
     * @return bool
     */
    public function validate(FileUpload $file): bool;

    /**
     * @return array
     */
    public function getErrors(): array;

    /**
     * @param string $error
     * @return static
     */
    public function addError(string $error);

    /**
     * @param string $key
     * @param string $error
     * @return static
     */
    public function setError(string $key, string $error);
}
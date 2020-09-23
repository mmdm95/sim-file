<?php

namespace Sim\File\Interfaces;

interface IUpload
{
    /**
     * @param string $destination
     * @param bool $overwrite
     * @return bool
     */
    public function upload(string $destination, bool $overwrite = false): bool;

    /**
     * @param string $name
     * @return static
     */
    public function setName(string $name);

    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @return string
     */
    public function getNameWithExtension(): string;

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

    /**
     * @return bool
     */
    public function hasError(): bool;
}
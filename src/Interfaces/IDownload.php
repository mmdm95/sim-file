<?php

namespace Sim\File\Interfaces;

interface IDownload
{
    /**
     * @param string|null $new_name
     * @return void
     */
    public function download(?string $new_name): void;

    /**
     * @return string|null
     */
    public function getPath(): ?string;

    /**
     * @param string $name
     * @return static
     */
    public function setName(string $name);

    /**
     * @return string|null
     */
    public function getName(): ?string;

    /**
     * @param string $extension
     * @return static
     */
    public function setExtension(string $extension);

    /**
     * @return string|null
     */
    public function getExtension(): ?string;

    /**
     * @return string
     */
    public function getNameWithExtension(): string;

    /**
     * @param string $mime_type
     * @return static
     */
    public function setMimeType(string $mime_type);

    /**
     * @return string|null
     */
    public function getMimeType(): ?string;

    /**
     * @return int
     */
    public function getSize(): int;

    /**
     * @return string
     */
    public function getFormattedSize(): string;

    /**
     * @param string $path
     * @return IDownload
     */
    public static function makeDownloadFromPath(string $path);
}
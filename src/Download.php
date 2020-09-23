<?php

namespace Sim\File;

use RuntimeException;
use Sim\File\Exceptions\DownloadException;
use Sim\File\Interfaces\IDownload;
use Sim\File\Utils\MimeTypeUtil;
use Sim\File\Utils\PathUtil;
use Sim\File\Utils\SizeUtil;

class Download implements IDownload
{
    /**
     * @var resource|null $file_pointer
     */
    protected $file_pointer = null;

    /**
     * @var string $name
     */
    protected $name = null;

    /**
     * @var string $mime_type
     */
    protected $mime_type = null;

    /**
     * @var string $path
     */
    protected $path = null;

    /**
     * @var int|false $size
     */
    protected $size = false;

    /**
     * @var string|null $extension
     */
    protected $extension = null;

    /**
     * Download constructor.
     * @param string $path
     * @throws DownloadException
     */
    public function __construct(string $path)
    {
        $this->path = PathUtil::getAbsolutePath($path, false);

        // check if path is specified
        if (empty($path) || !file_exists($path) || !is_file($path)) {
            throw new DownloadException('A existing file is required to start download.');
        }

        // check if file is readable
        if (!is_readable($path)) {
            throw new DownloadException('File is not readable!');
        }

        // get name, mimetype, path from path
        $this->getInfo();
    }

    /**
     * @see https://gist.github.com/vanita5/6293f77a5d9be686210b
     * {@inheritdoc}
     */
    public function download(?string $name = null): void
    {
        $path = $this->getPath();

        // check if headers not sent yet
        if (headers_sent()) {
            throw new RuntimeException("Headers were already sent!");
        }

        // Required for some browsers like Safari and IE
        if (ini_get('zlib.output_compression')) {
            ini_set('zlib.output_compression', 'Off');
        }

        //Set header
        header('Pragma: public');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Cache-Control: private', false); // required for some browsers
        header('Content-Type: ' . $this->getMimeType());
        header('Content-Disposition: attachment; filename="' . $this->getNameWithExtension() . '";');
        header('Content-Transfer-Encoding: binary');
        header('Content-Length: ' . $this->getSize());

        ob_clean();
        flush();
        readfile($path);
    }

    /**
     * {@inheritdoc}
     */
    public function getPath(): ?string
    {
        return $this->path;
    }

    /**
     * {@inheritdoc}
     */
    public function setName(string $name)
    {

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function setExtension(string $extension)
    {
        $this->extension = $extension;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getExtension(): ?string
    {
        return $this->extension;
    }

    /**
     * {@inheritdoc}
     */
    public function getNameWithExtension(): string
    {
        return $this->getName() . '.' . $this->getExtension();
    }

    /**
     * {@inheritdoc}
     */
    public function setMimeType(string $mime_type)
    {
        if (MimeTypeUtil::isValidMimeType($mime_type)) {
            $this->mime_type = $mime_type;
        }
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getMimeType(): ?string
    {
        return $this->mime_type;
    }

    /**
     * {@inheritdoc}
     */
    public function getSize(): int
    {
        return $this->size;
    }

    /**
     * {@inheritdoc}
     */
    public function getFormattedSize(): string
    {
        return SizeUtil::formatBytes($this->getSize());
    }

    /**
     * {@inheritdoc}
     * @throws DownloadException
     */
    public static function makeDownloadFromPath(string $path)
    {
        return new Download($path);
    }

    /**
     * Get info of a path like size, mimetype, extension, etc.
     */
    protected function getInfo()
    {
        $path = $this->getPath();
        $info = pathinfo($path);
        //-----
        $this->size = filesize($path);
        $this->setExtension(strtolower($info['extension']));
        $this->setName($info['filename']);
        $this->setMimeType(MimeTypeUtil::getMimeTypeFromFilename($path) ?? 'application/force-download');
    }
}
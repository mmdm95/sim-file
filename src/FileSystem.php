<?php

namespace Sim\File;

use Exception;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Sim\File\Filters\TypeFilter;
use Sim\File\Interfaces\IFileSystem;
use Sim\File\Interfaces\IFilter;
use Sim\File\Utils\MimeTypeUtil;
use Sim\File\Utils\PathUtil;

class FileSystem implements IFileSystem
{
    /**
     * @var string $filename
     */
    protected $filename;

    /**
     * FileSystem constructor.
     * @param string $filename
     */
    public function __construct(string $filename)
    {
        $this->filename = PathUtil::getAbsolutePath($filename, false);
    }

    /**
     * {@inheritdoc}
     */
    public function exists(): bool
    {
        return self::fileExists($this->filename);
    }

    /**
     * {@inheritdoc}
     */
    public static function fileExists(string $filename): bool
    {
        $filename = PathUtil::getAbsolutePath($filename, false);
        return file_exists($filename);
    }

    /**
     * {@inheritdoc}
     */
    public function isReadable(): bool
    {
        return self::isFileReadable($this->filename);
    }

    /**
     * {@inheritdoc}
     */
    public static function isFileReadable(string $filename): bool
    {
        $filename = PathUtil::getAbsolutePath($filename, false);
        return is_readable($filename);
    }

    /**
     * {@inheritdoc}
     */
    public function isWritable(): bool
    {
        return self::isFileWritable($this->filename);
    }

    /**
     * {@inheritdoc}
     */
    public static function isFileWritable(string $filename): bool
    {
        $filename = PathUtil::getAbsolutePath($filename, false);
        return is_file($filename) && is_writable($filename);
    }

    /**
     * {@inheritdoc}
     */
    public function isEmpty(): bool
    {
        return self::isDirEmpty($this->filename);
    }

    /**
     * @see https://stackoverflow.com/a/7497848/12154893
     * {@inheritdoc}
     */
    public static function isDirEmpty(string $dir): bool
    {
        if(!self::fileExists($dir) || !self::isFileReadable($dir)) return false;
        $dir = is_file($dir) ? dirname($dir) : $dir;

        $handle = opendir($dir);
        while (false !== ($entry = readdir($handle))) {
            if ($entry != "." && $entry != "..") {
                closedir($handle);
                return false;
            }
        }
        closedir($handle);
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function get($prefer = null): string
    {
        return self::getFromFile($this->filename, $prefer);
    }

    /**
     * {@inheritdoc}
     */
    public static function getFromFile(string $filename, $prefer = null): string
    {
        $filename = PathUtil::getAbsolutePath($filename, false);
        return self::isFileReadable($filename) && is_file($filename) ? file_get_contents($filename) : $prefer;
    }

    /**
     * {@inheritdoc}
     */
    public function read($prefer = null): string
    {
        return $this->get($prefer);
    }

    /**
     * {@inheritdoc}
     */
    public static function readFromFile(string $filename, $prefer = null): string
    {
        $filename = PathUtil::getAbsolutePath($filename, false);
        return self::getFromFile($filename, $prefer);
    }

    /**
     * {@inheritdoc}
     */
    public function put(string $data, ?int $mode = null)
    {
        return self::putToFile($this->filename, $data, $mode);
    }

    /**
     * {@inheritdoc}
     */
    public static function putToFile(string $filename, string $data, ?int $mode = null)
    {
        $filename = PathUtil::getAbsolutePath($filename, false);
        if (is_dir($filename)) return false;

        $chmod = !is_null($mode) && !is_file($filename);
        $res = @file_put_contents($filename, $data, LOCK_EX);
        if ($chmod) {
            self::fileChmod($filename, $mode);
        }

        return $res;
    }

    /**
     * {@inheritdoc}
     */
    public function write(string $data, ?int $mode = null)
    {
        return $this->put($data, $mode);
    }

    /**
     * {@inheritdoc}
     */
    public static function writeToFile(string $filename, string $data, ?int $mode = null)
    {
        $filename = PathUtil::getAbsolutePath($filename, false);
        return self::putToFile($filename, $data, $mode);
    }

    /**
     * {@inheritdoc}
     */
    public function append(string $data)
    {
        return self::appendToFile($this->filename, $data);
    }

    /**
     * {@inheritdoc}
     */
    public static function appendToFile(string $filename, string $data)
    {
        $filename = PathUtil::getAbsolutePath($filename, false);
        return @file_put_contents($filename, $data, LOCK_EX | FILE_APPEND);
    }

    /**
     * {@inheritdoc}
     */
    public function prepend(string $data): bool
    {
        return self::prependToFile($this->filename, $data);
    }

    /**
     * @see https://stackoverflow.com/questions/3332262/how-do-i-prepend-file-to-beginning
     * {@inheritdoc}
     */
    public static function prependToFile(string $filename, string $data): bool
    {
        $filename = PathUtil::getAbsolutePath($filename, false);
        try {
            $handle = @fopen($filename, "r+");
            $len = strlen($data);
            $final_len = self::getFileSize($filename) + $len;
            $cache_old = fread($handle, $len);
            rewind($handle);

            $i = 1;
            while (ftell($handle) < $final_len) {
                fwrite($handle, $data);
                $data = $cache_old;
                $cache_old = fread($handle, $len);
                fseek($handle, $i * $len);
                $i++;
            }
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function copy(string $new_destination, bool $overwrite = false): bool
    {
        return self::copyFile($this->filename, $new_destination, $overwrite);
    }

    /**
     * {@inheritdoc}
     */
    public static function copyFile(string $source, string $new_destination, bool $overwrite = false): bool
    {
        $source = PathUtil::getAbsolutePath($source, false);
        $new_destination = PathUtil::getAbsolutePath($new_destination, false);
        if (!self::fileExists($source)) {
            return false;
        }

        if (self::fileExists($new_destination) && !$overwrite) {
            return false;
        }

        // make sure the directory exists!
        self::makeDir(dirname($new_destination), IFileSystem::MODE_DIR_PUBLIC, true);

        return @copy($source, $new_destination);
    }

    /**
     * {@inheritdoc}
     */
    public function move(string $new_destination, bool $overwrite = false): bool
    {
        return self::moveFile($this->filename, $new_destination, $overwrite);
    }

    /**
     * {@inheritdoc}
     */
    public static function moveFile(string $source, string $new_destination, bool $overwrite = false): bool
    {
        $source = PathUtil::getAbsolutePath($source, false);
        $new_destination = PathUtil::getAbsolutePath($new_destination, false);
        if (!self::fileExists($source)) {
            return false;
        }

        if (self::fileExists($new_destination) && !$overwrite) {
            return false;
        }

        // make sure the directory exists!
        self::makeDir(dirname($new_destination), IFileSystem::MODE_DIR_PUBLIC, true);

        return @rename($source, $new_destination);
    }

    /**
     * {@inheritdoc}
     */
    public function rename(string $new_name, bool $overwrite = false): bool
    {
        return self::renameFile($this->filename, $new_name, $overwrite);
    }

    /**
     * {@inheritdoc}
     */
    public static function renameFile(string $old_name, string $new_name, bool $overwrite = false): bool
    {
        $old_name = PathUtil::getAbsolutePath($old_name, false);
        $new_name = PathUtil::getAbsolutePath($new_name, false);
        return self::moveFile($old_name, $new_name, $overwrite);
    }

    /**
     * {@inheritdoc}
     */
    public function delete(): bool
    {
        return self::deleteFile($this->filename);
    }

    /**
     * @see https://stackoverflow.com/questions/3349753/delete-directory-with-files-in-it - for directory deletion
     * {@inheritdoc}
     */
    public static function deleteFile(string $filename): bool
    {
        $filename = PathUtil::getAbsolutePath($filename, false);
        if (is_file($filename)) {
            @unlink($filename);
        } elseif (is_dir($filename)) {
            $it = new RecursiveDirectoryIterator($filename, RecursiveDirectoryIterator::SKIP_DOTS);
            $files = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST);
            foreach ($files as $file) {
                if ($file->isDir()) {
                    @rmdir($file->getRealPath());
                } else {
                    @unlink($file->getRealPath());
                }
            }
            if(self::isDirEmpty($filename)) {
                @rmdir($filename);
            }
        }

        return !self::fileExists($filename);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteFilteredFiles(array $filters = []): bool
    {
        return self::deleteDirFilteredFiles($this->filename, $filters);
    }

    /**
     * {@inheritdoc}
     */
    public static function deleteDirFilteredFiles(string $filename, array $filters = []): bool
    {
        $filters = array_filter($filters, function ($value) {
            return !$value instanceof TypeFilter;
        });
        $files = self::getDirFilteredFiles($filename, array_merge($filters, [new TypeFilter(self::TYPE_FILE)]));

        /**
         * @var \SplFileInfo $file
         */
        foreach ($files as $file) {
            @unlink($file->getRealPath());
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteAllFilteredFiles(array $filters = []): bool
    {
        return self::deleteDirAllFilteredFiles($this->filename, $filters);
    }

    /**
     * {@inheritdoc}
     */
    public static function deleteDirAllFilteredFiles(string $filename, array $filters = []): bool
    {
        $filename = PathUtil::getAbsolutePath($filename, false);
        $filename = is_file($filename) ? dirname($filename) : $filename;
        if (is_dir($filename)) {
            $it = new RecursiveDirectoryIterator($filename, RecursiveDirectoryIterator::SKIP_DOTS);
            $files = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST);

            /**
             * @var \SplFileInfo $file
             */
            foreach ($files as $file) {
                //----- Check filters
                $isValid = true;
                foreach ($filters as $filter) {
                    if (!$isValid) break;
                    if ($filter instanceof IFilter) {
                        $isValid = $isValid && $filter->filter($file);
                    }
                }
                //-----

                if ($isValid) {
                    if ($file->isDir()) {
                        @rmdir($file->getRealPath());
                    } else {
                        @unlink($file->getRealPath());
                    }
                }
            }
            if(self::isDirEmpty($filename)) {
                @rmdir($filename);
            }
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function chmod(string $mode): bool
    {
        return self::fileChmod($this->filename, $mode);
    }

    /**
     * {@inheritdoc}
     */
    public static function fileChmod(string $filename, string $mode): bool
    {
        $filename = PathUtil::getAbsolutePath($filename, false);
        return @chmod($filename, $mode);
    }

    /**
     * {@inheritdoc}
     */
    public function chown($user): bool
    {
        return self::fileChown($this->filename, $user);
    }

    /**
     * {@inheritdoc}
     */
    public static function fileChown(string $filename, $user): bool
    {
        $filename = PathUtil::getAbsolutePath($filename, false);
        return is_file($filename) && @chown($filename, $user);
    }

    /**
     * {@inheritdoc}
     */
    public function chgrp($group): bool
    {
        return self::fileChgrp($this->filename, $group);
    }

    /**
     * {@inheritdoc}
     */
    public static function fileChgrp(string $filename, $group): bool
    {
        $filename = PathUtil::getAbsolutePath($filename, false);
        return is_file($filename) && @chgrp($filename, $group);
    }

    /**
     * {@inheritdoc}
     */
    public function modificationTime()
    {
        return self::fileModificationTime($this->filename);
    }

    /**
     * {@inheritdoc}
     */
    public static function fileModificationTime(string $filename)
    {
        $filename = PathUtil::getAbsolutePath($filename, false);
        return is_file($filename) ? @filemtime($filename) : false;
    }

    /**
     * {@inheritdoc}
     */
    public function touch(?int $time = null, ?int $atime = null): bool
    {
        return self::touchFile($this->filename, $time, $atime);
    }

    /**
     * {@inheritdoc}
     */
    public static function touchFile(string $filename, ?int $time = null, ?int $atime = null): bool
    {
        $filename = PathUtil::getAbsolutePath($filename, false);
        return touch($filename, $time, $atime);
    }

    /**
     * {@inheritdoc}
     */
    public function getExtension($prefer = null): ?string
    {
        return self::getFileExtension($this->filename, $prefer);
    }

    /**
     * {@inheritdoc}
     */
    public static function getFileExtension(string $filename, $prefer = null): ?string
    {
        $filename = PathUtil::getAbsolutePath($filename, false);
        return is_file($filename) ? pathinfo($filename, PATHINFO_EXTENSION) : $prefer;
    }

    /**
     * {@inheritdoc}
     */
    public function getMimeType($prefer = null): ?string
    {
        return self::getFileMimeType($this->filename, $prefer);
    }

    /**
     * {@inheritdoc}
     */
    public static function getFileMimeType(string $filename, $prefer = null): ?string
    {
        $filename = PathUtil::getAbsolutePath($filename, false);
        return MimeTypeUtil::getMimeTypeFromFilename($filename) ?? $prefer;
    }

    /**
     * {@inheritdoc}
     */
    public function getOwner()
    {
        return self::getFileOwner($this->filename);
    }

    /**
     * {@inheritdoc}
     */
    public static function getFileOwner(string $filename)
    {
        $filename = PathUtil::getAbsolutePath($filename, false);
        if (!is_file($filename)) return false;
        return function_exists('posix_getpwuid') ? (self::getFileOwnerID($filename))['name'] : false;
    }

    /**
     * {@inheritdoc}
     */
    public function getOwnerID()
    {
        return self::getFileOwnerID($this->filename);
    }

    /**
     * {@inheritdoc}
     */
    public static function getFileOwnerID(string $filename)
    {
        $filename = PathUtil::getAbsolutePath($filename, false);
        if (!is_file($filename)) return false;
        return fileowner($filename);
    }

    /**
     * {@inheritdoc}
     */
    public function getGroup()
    {
        return self::getFileGroup($this->filename);
    }

    /**
     * {@inheritdoc}
     */
    public static function getFileGroup(string $filename)
    {
        $filename = PathUtil::getAbsolutePath($filename, false);
        if (!is_file($filename)) return false;
        return function_exists('posix_getpwuid') ? posix_getpwuid(self::getFileGroupID($filename))['name'] : false;
    }

    /**
     * {@inheritdoc}
     */
    public function getGroupID()
    {
        return self::getFileGroupID($this->filename);
    }

    /**
     * {@inheritdoc}
     */
    public static function getFileGroupID(string $filename)
    {
        $filename = PathUtil::getAbsolutePath($filename, false);
        if (!is_file($filename)) return false;
        return filegroup($filename);
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return self::getFileName($this->filename);
    }

    /**
     * {@inheritdoc}
     */
    public static function getFileName(string $filename): string
    {
        $filename = PathUtil::getAbsolutePath($filename, false);
        return pathinfo($filename, PATHINFO_FILENAME);
    }

    /**
     * {@inheritdoc}
     */
    public function getBasename(): string
    {
        return self::getFileBasename($this->filename);
    }

    /**
     * {@inheritdoc}
     */
    public static function getFileBasename(string $filename): string
    {
        $filename = PathUtil::getAbsolutePath($filename, false);
        return pathinfo($filename, PATHINFO_BASENAME);
    }

    /**
     * {@inheritdoc}
     */
    public function getInfo(int $options): array
    {
        return self::getFileInfo($this->filename, $options);
    }

    /**
     * {@inheritdoc}
     */
    public static function getFileInfo(string $filename, int $options): array
    {
        $filename = PathUtil::getAbsolutePath($filename, false);
        $info = [];
        $pathParts = pathinfo($filename);

        // filename included
        if ($options & self::INFO_FILENAME) {
            $info['filename'] = $pathParts['filename'];
        }
        // basename included
        if ($options & self::INFO_BASENAME) {
            $info['basename'] = $pathParts['basename'];
        }
        // dirname included
        if ($options & self::INFO_BASENAME) {
            $info['dirname'] = $pathParts['dirname'];
        }
        // size included
        if ($options & self::INFO_SIZE) {
            $info['size'] = self::getFileSize($filename);
        }
        // extension included
        if ($options & self::INFO_EXT) {
            $info['extension'] = $pathParts['extension'] ?? '';
        }
        // mimetype included
        if ($options & self::INFO_MIME_TYPE) {
            $info['mimetype'] = MimeTypeUtil::getMimeTypeFromFilename($filename);
        }
        // is readable included
        if ($options & self::INFO_IS_READABLE) {
            $info['is_readable'] = self::isFileReadable($filename);
        }
        // is writable included
        if ($options & self::INFO_IS_WRITABLE) {
            $info['is_writable'] = self::isFileWritable($filename);
        }
        // owner included
        if ($options & self::INFO_OWNER) {
            $info['owner'] = self::getFileOwner($filename);
        }
        // owner id included
        if ($options & self::INFO_OWNER_ID) {
            $info['owner_id'] = self::getFileOwnerID($filename);
        }
        // owner included
        if ($options & self::INFO_GROUP) {
            $info['group'] = self::getFileGroup($filename);
        }
        // owner id included
        if ($options & self::INFO_GROUP_ID) {
            $info['group_id'] = self::getFileGroupID($filename);
        }

        return $info;
    }

    /**
     * {@inheritdoc}
     */
    public function getSize(): int
    {
        return self::getFileSize($this->filename);
    }

    /**
     * @see https://gist.github.com/eusonlito/5099936 - for directory size recursively
     * {@inheritdoc}
     */
    public static function getFileSize(string $filename): int
    {
        $filename = PathUtil::getAbsolutePath($filename, false);
        $size = 0;
        if (is_file($filename)) {
            $size = filesize($filename);
        } elseif (is_dir($filename)) {
            foreach (glob(rtrim($filename, '/') . '/*', GLOB_NOSORT) as $each) {
                $size += is_file($each) ? filesize($each) : self::getFileSize($each);
            }
        }

        return $size;
    }

    /**
     * {@inheritdoc}
     */
    public function mkdir($mode = self::MODE_DIR_PUBLIC, bool $recursive = true): bool
    {
        return self::makeDir($this->filename, $mode, $recursive);
    }

    /**
     * {@inheritdoc}
     */
    public static function makeDir(string $dir, $mode = self::MODE_DIR_PUBLIC, bool $recursive = true): bool
    {
        $dir = PathUtil::getAbsolutePath($dir, false);
        return @mkdir($dir, $mode, $recursive);
    }

    /**
     * {@inheritdoc}
     */
    public function getAllFiles(int $type = self::TYPE_FILE | self::TYPE_DIRECTORY): ?RecursiveIteratorIterator
    {
        return self::getDirAllFiles($this->filename, $type);
    }

    /**
     * {@inheritdoc}
     */
    public static function getDirAllFiles(string $dir, int $type = self::TYPE_FILE | self::TYPE_DIRECTORY): ?RecursiveIteratorIterator
    {
        $dir = PathUtil::getAbsolutePath($dir, false);
        $files = null;
        $dir = is_file($dir) ? dirname($dir) : $dir;
        if (is_dir($dir)) {
            if (($type & self::TYPE_FILE) && ($type & self::TYPE_DIRECTORY)) {
                $theType = RecursiveIteratorIterator::SELF_FIRST;
            } elseif ($type & self::TYPE_FILE) {
                $theType = RecursiveIteratorIterator::LEAVES_ONLY;
            } elseif ($type & self::TYPE_DIRECTORY) {
                $theType = ~RecursiveIteratorIterator::LEAVES_ONLY;
            } else {
                $theType = RecursiveIteratorIterator::SELF_FIRST;
            }

            $it = new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS);
            /**
             * @var RecursiveIteratorIterator $files
             */
            $files = new RecursiveIteratorIterator($it, $theType);
        }

        return $files;
    }

    /**
     * {@inheritdoc}
     */
    public function getAllFilesInDepth(int $depth = 0, int $type = self::TYPE_FILE | self::TYPE_DIRECTORY): array
    {
        return self::getDirAllFilesInDepth($this->filename, $depth, $type);
    }

    /**
     * {@inheritdoc}
     */
    public static function getDirAllFilesInDepth(string $dir, int $depth = 0, int $type = self::TYPE_FILE | self::TYPE_DIRECTORY): array
    {
        $dir = PathUtil::getAbsolutePath($dir, false);
        return self::getDirAllFilteredFilesInDepth($dir, $depth, [new TypeFilter($type)]);
    }

    /**
     * {@inheritdoc}
     */
    public function getAllFilteredFilesInDepth(int $depth = 0, array $filters = []): array
    {
        return self::getDirAllFilteredFilesInDepth($this->filename, $depth, $filters);
    }

    /**
     * {@inheritdoc}
     */
    public static function getDirAllFilteredFilesInDepth(string $dir, int $depth = 0, array $filters = []): array
    {
        $dir = PathUtil::getAbsolutePath($dir, false);
        $files = [];
        $allFiles = self::getDirAllFiles($dir, self::TYPE_FILE | self::TYPE_DIRECTORY);

        // is recursive iterator null
        if (is_null($allFiles)) return [];

        $allFiles->rewind();
        $depth = $depth > $allFiles->getMaxDepth() ? $allFiles->getMaxDepth() : ($depth < 0 ? 0 : $depth);
        while ($allFiles->valid()) {
            $deep = $allFiles->getDepth();
            if ($deep == $depth) {
                /**
                 * @var \SplFileInfo $file
                 */
                $file = $allFiles->current();

                //----- Check filters
                $isValid = true;
                foreach ($filters as $filter) {
                    if (!$isValid) break;
                    if ($filter instanceof IFilter) {
                        $isValid = $isValid && $filter->filter($file);
                    }
                }
                //-----

                if ($isValid) {
                    $files[] = $file;
                }
            }

            $allFiles->next();
        }

        return $files;
    }

    /**
     * {@inheritdoc}
     */
    public function getFiles(int $type = self::TYPE_FILE | self::TYPE_DIRECTORY): array
    {
        return self::getDirFiles($this->filename, $type);
    }

    /**
     * {@inheritdoc}
     */
    public static function getDirFiles(string $dir, int $type = self::TYPE_FILE | self::TYPE_DIRECTORY): array
    {
        return self::getDirFilteredFiles($dir, [new TypeFilter($type)]);
    }

    /**
     * {@inheritdoc}
     */
    public function getFilteredFiles(array $filters = []): array
    {
        return self::getDirFilteredFiles($this->filename, $filters);

    }

    /**
     * {@inheritdoc}
     */
    public static function getDirFilteredFiles(string $dir, array $filters = []): array
    {
        $dir = PathUtil::getAbsolutePath($dir, false);
        $filteredFiles = [];
        $dir = is_file($dir) ? dirname($dir) : $dir;
        $files = array_diff(scandir($dir), array('.', '..'));

        foreach ($files as $file) {
            $splFI = new \SplFileInfo($dir . '/' . $file);

            //----- Check filters
            $isValid = true;
            foreach ($filters as $filter) {
                if (!$isValid) break;
                if ($filter instanceof IFilter) {
                    $isValid = $isValid && $filter->filter($splFI);
                }
            }
            //-----

            if ($isValid) {
                $filteredFiles[] = $splFI;
            }
        }

        return $filteredFiles;
    }
}
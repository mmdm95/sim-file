<?php

namespace Sim\File\Interfaces;

use RecursiveIteratorIterator;

interface IFileSystem
{
    // info flags
    const INFO_FILENAME = 1;
    const INFO_BASENAME = 2;
    const INFO_DIRNAME = 4;
    const INFO_SIZE = 8;
    const INFO_EXT = 16;
    const INFO_MIME_TYPE = 32;
    const INFO_IS_READABLE = 64;
    const INFO_IS_WRITABLE = 128;
    const INFO_OWNER = 256;
    const INFO_OWNER_ID = 512;
    const INFO_GROUP = 1024;
    const INFO_GROUP_ID = 2048;

    // modes
    const MODE_FILE_PUBLIC = 0644;
    const MODE_FILE_PRIVATE = 0600;
    const MODE_DIR_PUBLIC = 0755;
    const MODE_DIR_PRIVATE = 0700;

    // file types
    const TYPE_FILE = 1;
    const TYPE_DIRECTORY = 2;


    /**
     * @return bool
     */
    public function exists(): bool;

    /**
     * @param string $filename
     * @return bool
     */
    public static function fileExists(string $filename): bool;

    /**
     * @return bool
     */
    public function isReadable(): bool;

    /**
     * @param string $filename
     * @return bool
     */
    public static function isFileReadable(string $filename): bool;

    /**
     * @return bool
     */
    public function isWritable(): bool;

    /**
     * @param string $filename
     * @return bool
     */
    public static function isFileWritable(string $filename): bool;

    /**
     * @return bool
     */
    public function isFile(): bool;

    /**
     * @param string $filename
     * @return bool
     */
    public static function isItFile(string $filename): bool;

    /**
     * @return bool
     */
    public function isDir(): bool;

    /**
     * @param string $filename
     * @return bool
     */
    public static function isItDir(string $filename): bool;

    /**
     * Works only on directories
     *
     * @return bool
     */
    public function isEmpty(): bool;

    /**
     * Works only on directories
     *
     * @param string $dir
     * @return bool
     */
    public static function isDirEmpty(string $dir): bool;

    /**
     * @param null $prefer
     * @return string
     */
    public function get($prefer = null): string;

    /**
     * @param string $filename
     * @param mixed|null $prefer
     * @return string
     */
    public static function getFromFile(string $filename, $prefer = null): string;

    /**
     * Alias of get method
     *
     * @see get()
     * @param mixed|null $prefer
     * @return string
     */
    public function read($prefer = null): string;

    /**
     * Alias of getFromFile method
     *
     * @see getFromFile()
     * @param string $filename
     * @param mixed|null $prefer
     * @return string
     */
    public static function readFromFile(string $filename, $prefer = null): string;

    /**
     * @param string $data
     * @param int|null $mode
     * @return int|false
     */
    public function put(string $data, ?int $mode = null);

    /**
     * @param string $filename
     * @param string $data
     * @param int|null $mode
     * @return int|false
     */
    public static function putToFile(string $filename, string $data, ?int $mode = null);

    /**
     * Alias of put method
     *
     * @see put()
     * @param string $data
     * @param int|null $mode
     * @return int|false
     */
    public function write(string $data, ?int $mode = null);

    /**
     * Alias of putToFile method
     *
     * @see putToFile()
     * @param string $filename
     * @param string $data
     * @param int|null $mode
     * @return int|false
     */
    public static function writeToFile(string $filename, string $data, ?int $mode = null);

    /**
     * @param string $data
     * @return int|false
     */
    public function append(string $data);

    /**
     * @param string $filename
     * @param string $data
     * @return int|false
     */
    public static function appendToFile(string $filename, string $data);

    /**
     * @param string $data
     * @return bool
     */
    public function prepend(string $data): bool;

    /**
     * @param string $filename
     * @param string $data
     * @return bool
     */
    public static function prependToFile(string $filename, string $data): bool;

    /**
     * @param string $new_destination
     * @param bool $overwrite
     * @return bool
     */
    public function copy(string $new_destination, bool $overwrite = false): bool;

    /**
     * @param string $source
     * @param string $new_destination
     * @param bool $overwrite
     * @return bool
     */
    public static function copyFile(string $source, string $new_destination, bool $overwrite = false): bool;

    /**
     * @param string $new_destination
     * @param bool $overwrite
     * @return bool
     */
    public function move(string $new_destination, bool $overwrite = false): bool;

    /**
     * @param string $source
     * @param string $new_destination
     * @param bool $overwrite
     * @return bool
     */
    public static function moveFile(string $source, string $new_destination, bool $overwrite = false): bool;

    /**
     * @param string $new_name
     * @param bool $overwrite
     * @return bool
     */
    public function rename(string $new_name, bool $overwrite = false): bool;

    /**
     * @param string $old_name
     * @param string $new_name
     * @param bool $overwrite
     * @return bool
     */
    public static function renameFile(string $old_name, string $new_name, bool $overwrite = false): bool;

    /**
     * @return bool
     */
    public function delete(): bool;

    /**
     * @param string $filename
     * @return bool
     */
    public static function deleteFile(string $filename): bool;

    /**
     * Works only on directories
     *
     * Only delete files in first level of a directory
     *
     * @param array $filters
     * @return bool
     */
    public function deleteFilteredFiles(array $filters = []): bool;

    /**
     * Works only on directories
     *
     * Only delete files in first level of a directory
     *
     * @param string $filename
     * @param array $filters
     * @return bool
     */
    public static function deleteDirFilteredFiles(string $filename, array $filters = []): bool;

    /**
     * Works only on directories
     *
     * @param array $filters
     * @return bool
     */
    public function deleteAllFilteredFiles(array $filters = []): bool;

    /**
     * Works only on directories
     *
     * @param string $filename
     * @param array $filters
     * @return bool
     */
    public static function deleteDirAllFilteredFiles(string $filename, array $filters = []): bool;

    /**
     * @param string $mode
     * @return bool
     */
    public function chmod(string $mode): bool;

    /**
     * @param string $filename
     * @param string $mode
     * @return bool
     */
    public static function fileChmod(string $filename, string $mode): bool;

    /**
     * @param $user
     * @return bool
     */
    public function chown($user): bool;

    /**
     * @param string $filename
     * @param $user
     * @return bool
     */
    public static function fileChown(string $filename, $user): bool;

    /**
     * @param mixed $group
     * @return bool
     */
    public function chgrp($group): bool;

    /**
     * @param string $filename
     * @param mixed $group
     * @return bool
     */
    public static function fileChgrp(string $filename, $group): bool;

    /**
     * @return int|false
     */
    public function modificationTime();

    /**
     * @param string $filename
     * @return int|false
     */
    public static function fileModificationTime(string $filename);

    /**
     * @param int|null $time - Modification time
     * @param int|null $atime - Access time
     * @return bool
     */
    public function touch(?int $time = null, ?int $atime = null): bool;

    /**
     * @param string $filename
     * @param int|null $time - Modification time
     * @param int|null $atime - Access time
     * @return bool
     */
    public static function touchFile(string $filename, ?int $time = null, ?int $atime = null): bool;

    /**
     * @param mixed|null $prefer
     * @return string|null
     */
    public function getExtension($prefer = null): ?string;

    /**
     * @param string $filename
     * @param mixed|null $prefer
     * @return string|null
     */
    public static function getFileExtension(string $filename, $prefer = null): ?string;

    /**
     * @param mixed|null $prefer
     * @return string|null
     */
    public function getMimeType($prefer = null): ?string;

    /**
     * @param string $filename
     * @param mixed|null $prefer
     * @return string|null
     */
    public static function getFileMimeType(string $filename, $prefer = null): ?string;

    /**
     * @return string|false
     */
    public function getOwner();

    /**
     * It uses <em>posix_getpwuid</em> function, if
     * that function exists.
     *
     * @param string $filename
     * @return string|false
     */
    public static function getFileOwner(string $filename);

    /**
     * @return int|false
     */
    public function getOwnerID();

    /**
     * @param string $filename
     * @return int|false
     */
    public static function getFileOwnerID(string $filename);

    /**
     * @return string|false
     */
    public function getGroup();

    /**
     * It uses <em>posix_getpwuid</em> function, if
     * that function exists.
     *
     * @param string $filename
     * @return string|false
     */
    public static function getFileGroup(string $filename);

    /**
     * @return int|false
     */
    public function getGroupID();

    /**
     * @param string $filename
     * @return int|false
     */
    public static function getFileGroupID(string $filename);

    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @param string $filename
     * @return string
     */
    public static function getFileName(string $filename): string;

    /**
     * @return string
     */
    public function getBasename(): string;

    /**
     * @param string $filename
     * @return string
     */
    public static function getFileBasename(string $filename): string;

    /**
     * The info contains following:
     *   name
     *   size
     *   ext
     *   mime_type
     *   is_readable
     *   is_writable
     *   owner
     *   owner_id
     *   group
     *   group_id
     *
     * You can set wanted information through option parameter
     * Allowed options:
     *   INFO_NAME
     *   INFO_SIZE
     *   INFO_EXT
     *   INFO_MIME_TYPE
     *   INFO_IS_READABLE
     *   INFO_IS_WRITABLE
     *   INFO_OWNER
     *   INFO_OWNER_ID
     *   INFO_GROUP
     *   INFO_GROUP_ID
     *
     * @param int $options
     * @return array
     */
    public function getInfo(int $options): array;

    /**
     * The info contains following:
     *   name
     *   size
     *   ext
     *   mime_type
     *   is_readable
     *   is_writable
     *   owner
     *   owner_id
     *   group
     *   group_id
     *
     * You can set wanted information through option parameter
     * Allowed options:
     *   INFO_NAME
     *   INFO_SIZE
     *   INFO_EXT
     *   INFO_MIME_TYPE
     *   INFO_IS_READABLE
     *   INFO_IS_WRITABLE
     *   INFO_OWNER
     *   INFO_OWNER_ID
     *   INFO_GROUP
     *   INFO_GROUP_ID
     *
     * @param string $filename
     * @param int $options
     * @return array
     */
    public static function getFileInfo(string $filename, int $options): array;

    /**
     * @return int
     */
    public function getSize(): int;

    /**
     * @param string $filename
     * @return int
     */
    public static function getFileSize(string $filename): int;

    /**
     * @param int $mode
     * @param bool $recursive
     * @return bool
     */
    public function mkdir($mode = self::MODE_DIR_PUBLIC, bool $recursive = true): bool;

    /**
     * @param string $dir
     * @param int $mode
     * @param bool $recursive
     * @return bool
     */
    public static function makeDir(string $dir, $mode = self::MODE_DIR_PUBLIC, bool $recursive = true): bool;

    /**
     * Works only on directories
     *
     * Note:
     *   If specified directory is a file, it'll
     *   use directory name of the file
     *
     * Example of usage:
     *   $files->rewind();
     *   while ($files->valid()) {
     *     // $file is instance of [SplFileInfo] class
     *     $file = $files->current();
     *     $filename = $file->getFilename();
     *     $deep = $files->getDepth();
     *     $isDir = is_dir($filename);
     *
     *     if ($isDir) {
     *       // do whatever need if it was directory
     *     } else {
     *       // do whatever need if it was file
     *     }
     *
     *     $files->next();
     *   }
     *
     * @param int $type
     * @return RecursiveIteratorIterator|null
     */
    public function getAllFiles(int $type = self::TYPE_FILE | self::TYPE_DIRECTORY): ?RecursiveIteratorIterator;

    /**
     * Works only on directories
     *
     * Note:
     *   If specified directory is a file, it'll
     *   use directory name of the file
     *
     * Example of usage:
     *   $files->rewind();
     *   while ($files->valid()) {
     *     // $file is instance of [SplFileInfo] class
     *     $file = $files->current();
     *     $filename = $file->getFilename();
     *     $deep = $files->getDepth();
     *     $isDir = is_dir($filename);
     *
     *     if ($isDir) {
     *       // do whatever need if it was directory
     *     } else {
     *       // do whatever need if it was file
     *     }
     *
     *     $files->next();
     *   }
     *
     * @param string $dir
     * @param int $type
     * @return RecursiveIteratorIterator|null
     */
    public static function getDirAllFiles(string $dir, int $type = self::TYPE_FILE | self::TYPE_DIRECTORY): ?RecursiveIteratorIterator;

    /**
     * Works only on directories
     *
     * Note:
     *   If specified directory is a file, it'll
     *   use directory name of the file
     *
     * @param int $depth
     * @param int $type
     * @return array
     */
    public function getAllFilesInDepth(int $depth = 0, int $type = self::TYPE_FILE | self::TYPE_DIRECTORY): array;

    /**
     * Works only on directories
     *
     * Note:
     *   If specified directory is a file, it'll
     *   use directory name of the file
     *
     * @param string $dir
     * @param int $depth
     * @param int $type
     * @return array
     */
    public static function getDirAllFilesInDepth(string $dir, int $depth = 0, int $type = self::TYPE_FILE | self::TYPE_DIRECTORY): array;

    /**
     * Works only on directories
     *
     * Note:
     *   If specified directory is a file, it'll
     *   use directory name of the file
     *
     * @param int $depth
     * @param array $filters
     * @return array
     */
    public function getAllFilteredFilesInDepth(int $depth = 0, array $filters = []): array;

    /**
     * Works only on directories
     *
     * Note:
     *   If specified directory is a file, it'll
     *   use directory name of the file
     *
     * @param string $dir
     * @param int $depth
     * @param array $filters
     * @return array
     */
    public static function getDirAllFilteredFilesInDepth(string $dir, int $depth = 0, array $filters = []): array;

    /**
     * Works only on directories
     *
     * Note:
     *   If specified directory is a file, it'll
     *   use directory name of the file
     *
     * @param int $type
     * @return array - An array of type [SplFileInfo]
     */
    public function getFiles(int $type = self::TYPE_FILE | self::TYPE_DIRECTORY): array;

    /**
     * Works only on directories
     *
     * Note:
     *   If specified directory is a file, it'll
     *   use directory name of the file
     *
     * @param string $dir
     * @param int $type
     * @return array - An array of type [SplFileInfo]
     */
    public static function getDirFiles(string $dir, int $type = self::TYPE_FILE | self::TYPE_DIRECTORY): array;

    /**
     * Works only on directories
     *
     * Note:
     *   If specified directory is a file, it'll
     *   use directory name of the file
     *
     * @param array $filters
     * @return array - An array of type [SplFileInfo]
     */
    public function getFilteredFiles(array $filters = []): array;

    /**
     * Works only on directories
     *
     * Note:
     *   If specified directory is a file, it'll
     *   use directory name of the file
     *
     * @param string $dir
     * @param array $filters
     * @return array - An array of type [SplFileInfo]
     */
    public static function getDirFilteredFiles(string $dir, array $filters = []): array;
}
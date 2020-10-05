<?php

include_once '../../vendor/autoload.php';
//include_once '../../autoloader.php';

$filename = dirname(__DIR__);

$files = \Sim\File\FileSystem::getDirFilteredFiles($filename, [
    new \Sim\File\Filters\TypeFilter(\Sim\File\Interfaces\IFileSystem::TYPE_FILE),
//    new \Sim\File\Filters\SizeFilter('10kb', '3.5kb'),
    new \Sim\File\Filters\RegexFilter('/ad\.php$/'),
]);

/**
 * @var SplFileInfo $file
 */
foreach ($files as $file) {
    echo $file->getFilename() . PHP_EOL;
}

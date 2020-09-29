# Simplicity File System
A library for file management.

## Install
**composer**
```php 
composer require mmdm/sim-file
```

Or you can simply download zip file from github and extract it, 
then put file to your project library and use it like other libraries.

## How to use

It has three parts to use:

- Download
- Upload
- FileSystem

### Download

#### Basic Usage

```php
// create new Download instance
$download = new Download($path_to_your_file);
$download->download($your_new_name);

// or just use static function
$download = Download::makeDownloadFromPath($path_to_your_file);
$download->download($your_new_name);
```

#### Methods

#### `download(?string $name = null): void`

The download main method. If *null* passed as `$name`, the original 
file name will use as downloadable file name.

#### `getPath(): ?string`

Get the path of file.

#### `setName(string $name)`

Set downloadable file name.

#### `getName(): string`

Get name of downloadable file without extension.

#### `setExtension(string $extension)`

Set extension of downloadable file.

#### `getExtension(): ?string`

Get extension of downloadable file.

#### `getNameWithExtension(): string`

Get name of downloadable file with extension.

#### `setMimeType(string $mime_type)`

Set mimetype of downloadable file.

#### `getMimeType(): string`

Get mimetype of downloadable file.

#### `getSize(): int`

Get size of downloadable file.

#### `getFormattedSize(): string`

Get size of downloadable file in a human readable format like 
2MB or 4KB etc.

#### `static makeDownloadFromPath(string $path): IDownload`

Make a download instance from a path with static calling.

---

### Upload

To create a upload instance, you should pass a `FileUpload` 
instance to `Upload` constructor.

### `FileUpload`

You should pass the key of `$_FILES` that have uploaded file.

#### Basic Usage

```php
$fileUpload = new FileUpload($the_key_of_file_in_file_global_variable);
```

#### Methods

#### `setValidations(array $validations)`

You can pass some validations to validate a file while upload 
method called.

Validations are as below for now:

- ExtensionValidation

```php
// new extension validation instance
// pass array of allowed extensions without dot
$extValidation = new ExtensionValidation(['png', 'jpg', 'jpeg']);
```

- MimeTypeValidation

```php
// new mimetype validation instance
// pass array of allowed mimetypes
$mimetypeValidation = new MimeTypeValidation(['image/png', 'image/jpg']);
```

- SizeValidation

You should pass max size as first parameter and 
min size as second parameter

Passed parameters can be in following format:

size of file with one of ['B', 'KB', 'MB', 'TB', 'PB']

**Note:** If unit is not specified, it'll be in bytes.

```php
// new size validation instance
$sizeValidation = new SizeValidation('2MB', '1MB');
```

#### `getValidations(): array`

Get array of validations.

#### `getExtension(): string`

Get extension of uploaded file.

#### `getSize(): int`

Get size of uploaded file.

#### `getOriginalName(): string`

Get original name of uploaded file without extension.

#### `getOriginalNameWithExtension(): string`

Get original name of uploaded file with extension.

#### `getErrors(): array`

Get errors of uploaded file. Basically it is standard error 
of uploaded file error and can have other errors too.

**Note:** Validation errors is not included here.

#### `addError(string $error)`

Add error to end of errors list.

#### `setError($key, string $error)`

Set error as a specific error in errors list.

#### `hasError(): bool`

Check if any error is set.

#### `errorMessagesTranslate(array $translate)`

Translate standard errors to your locally language.

#### Basic Usage

```php
// create new Upload instance
$upload = new Upload();
$upload->upload($your_new_destination);
```

#### Methods

#### `upload(string $destination, bool $overwrite = false): bool`

Do upload operation. Default behavior is to not overwrite existing file.

#### `setName(string $name)`

Set new name of uploaded file.

#### `getName(): string`

Get new name of uploaded file without extension. Default name is 
original name unless it change.

#### `getNameWithExtension(): string`

Get new name of uploaded file with extension.

#### `getErrors(): array`

Get errors during upload progress including validation errors.

#### `addError(string $error)`

Add error end of errors list.

#### `setError(string $key, string $error)`

Set error as a specific error in errors list.

#### `hasError(): bool`

Check if any error is set.

---

### FileSystem

You can use all methods in both normal instantiable way or 
use static methods of file system.

These methods works on both files and directories.

**Note:** Some of these methods that work only with directories, 
and have a little comment on method documentation that says:

> Works only on directories.

## Normal instantiable way methods:

#### `exists(): bool`

Check if specified file is exists.

#### `isReadable(): bool`

Check if specified file is readable.

#### `isWritable(): bool`

Check if specified file is writable.

#### `get($prefer = null): string`

Get all contents of a file as string

#### `read($prefer = null): string`

Alias of `get` method.

#### `put(string $data, ?int $mode = null)`

Write some string data to a file and return number of bytes 
that were written to the file, or false on failure.

#### `write(string $data, ?int $mode = null)`

Alias of `write` method.

#### `append(string $data)`

Append some string data to a file and return number of bytes 
that were written to the file, or false on failure.

#### `prepend(string $data): bool`

Append some string data to beginning of a file.

#### `copy(string $new_destination, bool $overwrite = false): bool`

Copy a file to a new destination. Use *true* as `$overwrite` 
parameter if want to overwrite the existence file.

#### `move(string $new_destination, bool $overwrite = false): bool`

Move a file to a new destination. Use *true* as `$overwrite` 
parameter if want to overwrite the existence file.

#### `rename(string $new_name, bool $overwrite = false): bool`

Rename a file with new name. Use *true* as `$overwrite` 
parameter if want to overwrite the existence file.

#### `delete(bool $recursive = true): bool`

Do delete operation. Use *true* as `$recursive` parameter if want 
to delete a directory recursively.

#### `chmod(string $mode): bool`

Change mode of a file.

#### `chown($user): bool`

Change owner/user of a file.

#### `chgrp($group): bool`

Change group of a file.

#### `modificationTime()`

Get modification time of a file.

#### `touch(?int $time = null, ?int $atime = null): bool`

Change the touch time and access time of a file.

#### `getExtension($prefer = null): ?string`

Get extension of a file or returns `$prefer`.

#### `getMimeType($prefer = null): ?string`

Get mime type of a file or returns `$prefer`.

#### `getOwner()`

Get user name of a file or false.

#### `getOwnerID()`

The user ID of the owner of the file, or false on failure.

#### `getGroup()`

Get file's group.

#### `getGroupID()`

Get file's group id.

#### `getName(): string`

Get file's name.

#### `getBasename(): string`

Get file's basename.

#### `getInfo(int $options): array`

Returns file's information. Information can be one or many of 
following constants (meaning of constant is obvious):

- INFO_FILENAME

- INFO_BASENAME

- INFO_DIRNAME

- INFO_SIZE

- INFO_EXT 

- INFO_MIME_TYPE 

- INFO_IS_READABLE
 
- INFO_IS_WRITABLE

- INFO_OWNER

- INFO_OWNER_ID

- INFO_GROUP

- INFO_GROUP_ID

#### `getSize(): int`

Get size of a file.

**Note:** Size of directories will be calculated recursively.

#### `mkdir($mode = self::MODE_DIR_PUBLIC, bool $recursive = true): bool`

Make a directory with a specific mode. To make directory recursively, 
use *true* as `$recursive` parameter.

**Note:** This method is only for directories.

#### `getAllFiles(int $type = self::TYPE_FILE | self::TYPE_DIRECTORY): ?RecursiveIteratorIterator`

Will return a `RecursiveIteratorIterator` to iterate through all files 
or null on error.

**Note:** This method is only for directories.

**Note:** If you want a file's files, it'll use directory of that 
file as needed directory to get files from.

**Note:** Please see [this link](https://www.php.net/manual/en/class.recursiveiteratoriterator.php) 
for more information about `RecursiveIteratorIterator` methods.

An example of usage:

```php
$files = $fileSystem->getAllFiles();
$files->rewind();
while ($files->valid()) {
  /**
  * @var SplFileInfo $file
  */ 
  $file = $files->current();
  
  // do whatever you need with that file
  
  $files->next();
}
```

#### `getAllFilesInDepth(int $depth = 0, int $type = self::TYPE_FILE | self::TYPE_DIRECTORY): array`

Will return an array of files of type `SplFileInfo` from depth 
of `$depth`.

**Note:** This method is only for directories.

**Note:** If you want a file's files, it'll use directory of that 
file as needed directory to get files from.

#### `getAllFilteredFilesInDepth(int $depth = 0, array $filters = []): array`

Will return an array of filtered files of type `SplFileInfo` from 
depth of `$depth`.

**Note:** This method is only for directories.

**Note:** If you want a file's files, it'll use directory of that 
file as needed directory to get files from.

Filters are similar to `Validations` from upload section.

Filter classes are:

- ExtensionFilter

```php
// new extension filterer instance
// pass array of allowed extensions without dot
$extFilter = new ExtensionFilter(['png', 'jpg', 'jpeg']);
```

- MimeTypeFilter

```php
// new mimetype filterer instance
// pass array of allowed mimetypes
$mimetypeFilter = new MimeTypeFilter(['image/png', 'image/jpg']);
```

- NameFilter

You should pass a regex according to name of file you need 
without extension to constructor.

```php
// new name filterer instance
// files that name of them ends with 'es'
$nameFilter = new NameFilter('/es$/i');
```

- SizeFilter

You should pass max size as first parameter and 
min size as second parameter

Passed parameters can be in following format:

size of file with one of ['B', 'KB', 'MB', 'TB', 'PB']

**Note:** If unit is not specified, it'll be in bytes.

```php
// new size filterer instance
$sizeFilter = new SizeFilter('2MB', '1MB');
```

- TypeFilter

There are two types: *file* and *directory* that can specify 
through constants below:

- IFileSystem::TYPE_FILE

- IFileSystem::TYPE_DIRECTORY

```php
// new type filterer instance
$typeFilter = new TypeFilter(IFileSystem::TYPE_FILE | IFileSystem::TYPE_DIRECTORY);
```

- RegexFilter

You should pass a regex to filter a file and it'll apply on name 
and extension

```php
// new regex filterer instance
$regexFilter = new RegexFilter('/es\.(png|jpe?g|gif)$/i');
```

#### `getFiles(int $type = self::TYPE_FILE | self::TYPE_DIRECTORY): array`

Get files of a specific directory as array of type `SplFileInfo`.

**Note:** This method is only for directories.

**Note:** If you want a file's files, it'll use directory of that 
file as needed directory to get files from.

#### `getFilteredFiles(array $filters = []): array`

Get filtered files of a specific directory as array of 
type `SplFileInfo`.

Filters are like the filters that have been explained above.

**Note:** This method is only for directories.

**Note:** If you want a file's files, it'll use directory of that 
file as needed directory to get files from.

## Static calling way methods:

Only difference of normal way and static way is, you should pass 
the file path you need to do the operation, to static methods.

#### `fileExists(string $filename): bool`

See `exists` in normal way.

#### `isFileReadable(string $filename): bool`

See `isReadable` in normal way.

#### `isFileWritable(string $filename): bool`

See `isWritable` in normal way.

#### `getFromFile(string $filename, $prefer = null): string`

See `get` in normal way.

#### `readFromFile(string $filename, $prefer = null): string`

See `read` in normal way.

#### `putToFile(string $filename, string $data, ?int $mode = null)`

See `put` in normal way.

#### `writeToFile(string $filename, string $data, ?int $mode = null)`

See `write` in normal way.

#### `appendToFile(string $filename, string $data)`

See `append` in normal way.

#### `prependToFile(string $filename, string $data): bool`

See `prepend` in normal way.

#### `copyFile(string $source, string $new_destination, bool $overwrite = false): bool`

See `copy` in normal way.

#### `moveFile(string $source, string $new_destination, bool $overwrite = false): bool`

See `move` in normal way.

#### `renameFile(string $old_name, string $new_name, bool $overwrite = false): bool`

See `rename` in normal way.

#### `deleteFile(string $filename, bool $recursive = true): bool`

See `delete` in normal way.

#### `fileChmod(string $filename, string $mode): bool`

See `chmod` in normal way.

#### `fileChown(string $filename, $user): bool`

See `chown` in normal way.

#### `fileChgrp(string $filename, $group): bool`

See `chgrp` in normal way.

#### `fileModificationTime(string $filename)`

See `modificationTime` in normal way.

#### `touchFile(string $filename, ?int $time = null, ?int $atime = null): bool`

See `touch` in normal way.

#### `getFileExtension(string $filename, $prefer = null): ?string`

See `getExtension` in normal way.

#### `getFileMimeType(string $filename, $prefer = null): ?string`

See `getMimeType` in normal way.

#### `getFileOwner(string $filename)`

See `getOwner` in normal way.

#### `getFileOwnerID(string $filename)`

See `getOwnerID` in normal way.

#### `getFileGroup(string $filename)`

See `getGroup` in normal way.

#### `getFileGroupID(string $filename)`

See `getGroupID` in normal way.

#### `getFileName(string $filename): string`

See `getName` in normal way.

#### `getFileBasename(string $filename): string`

See `getBasename` in normal way.

#### `getFileInfo(string $filename, int $options): array`

See `getInfo` in normal way.

#### `getFileSize(string $filename): int`

See `getSize` in normal way.

#### `makeDir(string $dir, $mode = self::MODE_DIR_PUBLIC, bool $recursive = true): bool`

See `mkdir` in normal way.

#### `getDirAllFiles(string $dir, int $type = self::TYPE_FILE | self::TYPE_DIRECTORY): ?RecursiveIteratorIterator`

See `getAllFiles` in normal way.

#### `getDirAllFilesInDepth(string $dir, int $depth = 0, int $type = self::TYPE_FILE | self::TYPE_DIRECTORY): array`

See `getAllFilesInDepth` in normal way.

#### `getDirAllFilteredFilesInDepth(string $dir, int $depth = 0, array $filters = []): array`

See `getAllFilteredFilesInDepth` in normal way.

#### `getDirFiles(string $dir, int $type = self::TYPE_FILE | self::TYPE_DIRECTORY): array`

See `getFiles` in normal way.

#### `getDirFilteredFiles(string $dir, array $filters = []): array`

See `getFilteredFiles` in normal way.

### How to add more validations

To add your validation, you must extend from `AbstractValidator` 
from `Abstracts` directory and implement following method(s):

#### `validate(FileUpload $file): bool`

Main validation method that need a file of type `FileUpload`.

### How translate validations error(s)

To translate validations error(s) you need to use 
`setMessage($key, $message)` method inside you class after 
extending `AbstractValidator` and set your locally message to 
specific error key.

Key of errors in each class are:

- ExtensionValidation

```php
[
  'extension' => 'Specified extension is not allowed!'
]
```

- MimeTypeValidation

```php
[
  'mimetype' => 'Specified mimetype is not allowed!',
]
```

- SizeValidation

```php
[
  'gt_size' => 'File size is greater than allowed file size!',
  'lt_size' => 'File size is less than allowed file size!',
]
```

### How to add more filters

To add your filter, you must implement `IFilter` interface from 
`Interfaces` directory and implement following method(s):

#### `filter(SplFileInfo $file): bool`

Main filter method that need a file of type `SplFileInfo`.

# License
Under MIT license.
<?php

namespace Sim\File\Exceptions;

use Exception;
use Sim\File\Interfaces\IFileSystemException;

class DownloadException extends Exception implements IFileSystemException
{

}
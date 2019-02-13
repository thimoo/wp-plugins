<?php

namespace WPHelper;

use \ErrorException;

/**
 * Class Base
 */
class Base
{
    public static function create()
    {
        return new static();
    }

    /**
     * Base constructor
     */
    public function __construct()
    {
        set_error_handler([$this, 'errorHandler']);
    }

    /**
     * Base destructor
     */
    public function __destruct()
    {
        restore_error_handler();
    }

    /**
     * Turns errors into exceptions
     * @param $errNo
     * @param $errStr
     * @param $errFile
     * @param $errLine
     * @param array $errContext
     * @return bool
     * @throws ErrorException
     */
    public function errorHandler($errNo, $errStr, $errFile, $errLine, array $errContext)
    {
        // error was suppressed using @statement
        if (error_reporting() === 0) {
            return false;
        }

        throw new ErrorException($errStr, 0, $errNo, $errFile, $errLine);
    }
}

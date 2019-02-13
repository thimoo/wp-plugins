<?php

namespace WPHelper;

/**
 * Class Loader
 * Helps to find WordPress classes
 *
 * @package WPHelper
 */
class Loader extends Base
{
    /**
     * Loader constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Search wp-load.php
     * @param bool $autoload Whether to autoload the file or not
     * @return bool|string
     */
    public static function searchWordPress($autoload = false)
    {
        $file = Common::locateFile('wp-load.php');

        if ($autoload && $file !== false) {
            require_once($file);
        }

        return $file;
    }
}

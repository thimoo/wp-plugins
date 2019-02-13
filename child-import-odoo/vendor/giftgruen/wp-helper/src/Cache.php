<?php

namespace WPHelper;

/**
 * Class Cache
 * Helps caching uploads
 * [Still WiP]
 *
 * @deprecated
 * @package WPHelper
 */
class Cache
{
    /**
     * The file where meta-data is saved
     * @var string
     */
    private $CACHE_FILE;

    /**
     * The path where the cache is kept
     * @var string
     */
    private $CACHE_DIR;

    /**
     * Cache constructor
     *
     * @param $path
     * @param int $invalidateAfter
     */
    public function __construct($path, $invalidateAfter = 86400)
    {
        if (!file_exists($path)) {
            mkdir($path);
            $this->CACHE_DIR = $path;
            $this->CACHE_FILE = $path . '/cache-meta.json';
        }
    }

    /**
     * Warmup the cache
     */
    public function warmup()
    {
        if (!file_exists($this->CACHE_FILE)) {
            $handle = fopen($this->CACHE_FILE, 'r+b');
            fwrite($handle, json_encode([]));
            fclose($handle);
        } else {
            $this->flush();
        }
    }

    /**
     * Flush the cache
     * @param bool $ignoreValidation Ignore timings and simply flush ALL THE THINGS [danger-zone]
     */
    public function flush($ignoreValidation = false)
    {
        $meta = json_decode(file_get_contents($this->CACHE_FILE));
        if (sizeof($meta) > 0) {
            foreach ($meta as $k => $v) {
                if ($ignoreValidation || $v <= strtotime('now')) {
                    unlink($this->CACHE_DIR . $k);
                }
            }
        }
    }
}

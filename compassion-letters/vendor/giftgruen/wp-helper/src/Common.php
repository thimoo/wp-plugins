<?php

namespace WPHelper;

/**
 * Common class
 *
 * Contains helpers that fit nowhere else
 *
 * @package WPHelper
 */
class Common
{
    /**
     * Register a custom post type
     *
     * @param String $name The post-type name
     * @param String|null $id The post-type id
     * @param array $additionalArgs Any additional args
     * @see https://developer.wordpress.org/reference/functions/register_post_type/
     */
    public static function registerPostType($name, $id = null, $additionalArgs = [])
    {
        if ($id == null) {
            $id = strtolower($name);
        }

        $labels = [
            'name' => $name,
            'menu_name' => $name,
            'name_admin_bar' => $name,
            'add_new' => 'Datensatz hinzufügen',
            'add_new_item' => 'Datensatz hinzufügen',
            'new_item' => 'Neuer Datensatz',
            'edit_item' => 'Datensatz bearbeiten',
            'view_item' => 'Datensatz ansehen',
            'all_items' => 'Alle Datensätze',
            'search_items' => 'Datensätze durchsuchen',
            'not_found' => 'Datensatz nicht gefunden!',
            'not_found_in_trash' => 'Gelöschter Datensatz nicht gefunden!'
        ];

        $postArgs = [
            'labels' => $labels,
            'public' => true,
            'publicly_queryable' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'query_var' => true,
            'capability_type' => 'post',
            'hierarchical' => false,
        ];

        if (sizeof($additionalArgs) > 0) {
            foreach ($additionalArgs as $k => $v) {
                $postArgs[$k] = $v;
            }
        }

        register_post_type($id, $postArgs);
    }

    /**
     * Creates a new post
     *
     * @param string $title Post title
     * @param string $status Post status
     * @param array $meta Post meta fields. <br> Set it to [] if you want no meta fields.
     * @param array $additionalArgs Any additional args
     * @see https://developer.wordpress.org/reference/functions/wp_insert_post/
     * @return mixed
     */
    public static function createPost($title, $status = 'draft', $meta = [], $additionalArgs = [])
    {
        $args = [
            'post_title' => $title,
            'post_status' => $status,
            'meta_input' => $meta
        ];

        foreach ($additionalArgs as $k => $v) {
            $args[$k] = $v;
        }

        return wp_insert_post($args);
    }

    /**
     * Shortcut to hash something
     * @param $thing
     * @return string
     */
    public static function hash($thing)
    {
        return hash('ripemd160', $thing);
    }

    /**
     * Store something in the current (boxed) session
     *
     * @param $key
     * @param $item
     */
    public static function addSession($key, $item)
    {
        if (!is_array($_SESSION[self::hash(__CLASS__)])) {
            $_SESSION[self::hash(__CLASS__)] = [];
        }

        $_SESSION[self::hash(__CLASS__)][$key] = $item;
    }

    /**
     * Get something from the current (boxed) session
     * @param $key
     * @return mixed
     */
    public static function getSession($key)
    {
        return $_SESSION[self::hash(__CLASS__)][$key];
    }

    /**
     * Flush/Clear the current (boxed) session
     */
    public static function flushSession()
    {
        $_SESSION[self::hash(__CLASS__)] = null;
    }

    /**
     * Resolve the temporary file from a form name
     * @param $formName
     * @param null $files
     * @return mixed
     */
    public static function getTempFile($formName, $files = null)
    {
        $files = ($files === null ? $_FILES : $files);
        return $files[$formName]['tmp_name'];
    }

    /**
     * Get a file by it's form name
     *
     * @param $formName
     * @param $directory
     * @param null $files
     * @return bool|string
     */
    public static function getFile($formName, $directory, $files = null)
    {
        $files = ($files === null ? $_FILES : $files);

        $tmp = self::getTempFile($formName, $files);
        $extension = explode('.', trim($files[$formName]['name']));
        $extension = $extension[sizeof($extension) - 1];

        $target = $directory . '/' . hash('md5', $formName . strtotime('now')) . '.' . $extension;

        if ($files[$formName]['error'] === UPLOAD_ERR_OK && $files[$formName]['size'] > 0) {
            if (is_uploaded_file($tmp)) {
                move_uploaded_file($tmp, $target);
                return $target;
            }
        }

        return false;
    }

    public static function locateFile($file)
    {
        $start = getcwd();
        $found = false;

        try {
            while (!$found) {
                $files = scandir(getcwd());
                $search = array_search($file, $files);

                if ($search !== false) {
                    $found = true;

                    $path = getcwd() . '/' . $file;

                    chdir($start);

                    return $path;
                } else {
                    chdir('..');
                }
            }
        } catch (\ErrorException $e) {
            chdir($start);
            return false;
        }

        chdir($start);
        return false;
    }
}

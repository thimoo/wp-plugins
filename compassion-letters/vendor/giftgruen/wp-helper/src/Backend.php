<?php

namespace WPHelper;

/**
 * Backend class
 *
 * Contains helpers commonly used to modify/access the backend
 *
 * @package WPHelper
 */
class Backend
{
    /**
     * Register a custom metabox
     *
     * @param String $title Metabox title
     * @param String $postType Post type to link
     * @param callable|array $callback Callback that draws the metabox
     * @param String|null $id Metabox id
     * @return $this
     */
    public static function registerMetaBox($title, $postType, $callback, $id = null)
    {
        if ($id == null) {
            $id = $postType . '_cmb_' . strtolower(trim($title));
        }

        if (defined($callback['args'])) {
            \add_meta_box($id, $title, $callback, $postType, 'advanced', 'default');
        } else {
            \add_meta_box($id, $title, $callback['callback'], $postType, 'advanced', 'default', $callback['args']);
        }
    }

    /**
     * Register a custom admin page
     *
     * @param string $pageName Name of the Page
     * @param callable|array $callback Callback that draws the page
     * @param string $icon [Dashicon ID] Icon shown in the backend gutter
     * @param int|null $position Position in the backend gutter
     * @return $this
     */
    public static function registerAdminPage($pageName, $callback, $icon = 'dashicons-admin-plugins', $position = null, $slug = null)
    {
        if ($slug === null) {
            $slug = Common::hash($pageName);
        }

        \add_menu_page(
            $pageName,
            $pageName,
            'manage_options',
            $slug,
            $callback,
            $icon,
            $position
        );
    }
}

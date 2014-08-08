<?php

class tad_Plugin
{
    /**
     * @var string The plugin name, e.g. "Hello Dolly".
     */
    protected $pluginName;
    /**
     * @var string The plugin slug, e.g. "hello-dolly".
     */
    protected $pluginSlug;
    /**
     * @var string The path to the plugin file relative to the plugins folder, e.g. "my-plugin/my-plugin.php".
     */
    protected $pluginFile;
    /**
     * @var tad_FunctionsAdapter|tad_FunctionsAdapterInterface an instance of the global functions adapter.
     */
    protected $wpf;

    /**
     * @param $pluginName The plugin name, e.g. "Hello Dolly".
     * @param $pluginSlug The plugin slug, e.g. "hello-dolly".
     * @param $pluginFile The path to the plugin file relative to the plugins folder, e.g. "my-plugin/my-plugin.php".
     * @param tad_FunctionsAdapterInterface $functions The path to the plugin file relative to the plugins folder, e.g. "my-plugin/my-plugin.php".
     * @throws BadArgumentException
     */
    public function __construct($pluginName, $pluginSlug, $pluginFile, tad_FunctionsAdapterInterface $functions = null)
    {
        if (!is_string($pluginName)) {
            throw new BadArgumentException('Plugin name must be a string', 1);
        }
        if (!is_string($pluginSlug)) {
            throw new BadArgumentException('Plugin slug must be a string', 2);
        }
        if (!is_string($pluginFile)) {
            throw new BadArgumentException('Plugin root folder must be a string', 3);
        }
        if (!preg_match("/[\\w-]*\\/*[\\w-]+\\.php/", $pluginFile)) {
            throw new BadArgumentException('Plugin file must be in the "plugin-folder/plugin-file.php" format.', 4);
        }
        $this->wpf = $functions ? $functions : new tad_FunctionsAdapter();
    }

    /**
     * Generate an installation URL for a plugin like the ones found on the Add New Plugin search results screen.
     *
     * @return string             The plugin installation url
     */
    public function getInstallationLink()
    {
        $installUrl = $this->wpf->admin_url('update.php?action=install-plugin&plugin=' . $this->pluginSlug);
        $installUrl = $this->wpf->wp_nonce_url($installUrl, 'install-plugin_' . $this->pluginSlug);

        return $installUrl;
    }

    /**
     * Return the anchor tag for the plugin installation link.
     *
     * @param array $classes An array of classes to assign to the anchor, defaults to none.
     * @param null $id The id to assign to the anchor, defaults to none.
     * @param string $title The link title, defaults to 'install plugin-name'.
     *
     * @return mixed
     */
    public function theInstallationLink(array $classes = null, $id = null, $title = null)
    {
        $link = $this->getInstallationLink();
        $title = is_string($title) ? $title : sprintf('install %s', $this->pluginName);
        return $this->getActionLink($classes, $id, $title, $link);
    }

    /**
     * Generate an activation URL for a plugin like the ones found in WordPress plugin administration screen.
     *
     * @return string         The plugin activation url
     */
    public function getActivationLink()
    {
        // the plugin might be located in the plugin folder directly

        if (strpos($this->pluginFile, '/')) {
            $pluginFile = str_replace('/', '%2F', $this->pluginFile);
        }

        $activateUrl = sprintf(admin_url('plugins.php?action=activate&plugin=%s&plugin_status=all&paged=1&s'), $pluginFile);

        // change the plugin request to the plugin to pass the nonce check
        $_REQUEST['plugin'] = $pluginFile;
        $activateUrl = wp_nonce_url($activateUrl, 'activate-plugin_' . $pluginFile);

        return $activateUrl;
    }

    /**
     * Return the anchor tag for the plugin activation link.
     *
     * @param array $classes An array of classes to assign to the anchor, defaults to none.
     * @param null $id The id to assign to the anchor, defaults to none.
     * @param string $title The link title, defaults to 'activate plugin-name'.
     *
     * @return mixed
     */
    public function theActivationLink(array $classes = null, $id = null, $title = null)
    {
        $link = $this->getActivationLink();
        $title = is_string($title) ? $title : sprintf('activate %s', $this->pluginName);
        return $this->getActionLink($classes, $id, $title, $link);
    }

    protected function getActionLink(array $classes, $id, $title, $link)
    {
        $class = is_array($classes) ? sprintf('class="%s"', implode(' ', $classes)) : '';
        $id = is_string($id) ? sprintf('id="%s"', $id) : '';
        return sprintf('<a href="%s" class="%s" id="%s">%s</a>', $link, $class, $id, $title);
    }

    /**
     * Checks if a WordPress plugin is installed.
     *
     * @param  string /null $pluginTitle The plugin title (e.g. "My Plugin"), defautls to plugin name.
     *
     * @return string/boolean       The plugin file/folder relative to the plugins folder path (e.g. "my-plugin/my-plugin.php") or false if the plugin is not installed.
     */
    public function is_plugin_installed($pluginTitle = null)
    {
        $pluginTitle = is_string($pluginTitle) ? $pluginTitle : $this->pluginName;
        // get all the plugins
        $installedPlugins = $this->wpf->get_plugins();

        foreach ($installedPlugins as $installedPlugin => $data) {

            // check for the plugin title
            if ($data['Title'] == $pluginTitle) {

                // return the plugin folder/file
                return $installedPlugin;
            }
        }

        return false;
    }
}
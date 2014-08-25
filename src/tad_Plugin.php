<?php
class tad_Plugin extends tad_Object
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
     * @var array An array of required plugins in the format [title -> [url, file, slug]]
     */
    protected $requiredPlugins = array();
    
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
        $this->pluginName = $pluginName;
        $this->pluginSlug = $pluginSlug;
        $this->pluginFile = $pluginFile;
        $this->setFunctionsAdapter($functions);
    }
    
    /**
     * Generate an installation URL for a plugin like the ones found on the Add New Plugin search results screen.
     *
     * @return string             The plugin installation url
     */
    protected function generateInstallationLink()
    {
        $installUrl = $this->f->admin_url('update.php?action=install-plugin&plugin=' . $this->pluginSlug);
        $installUrl = $this->f->wp_nonce_url($installUrl, 'install-plugin_' . $this->pluginSlug);
        
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
    public function getInstallationLink(array $classes = null, $id = null, $title = null)
    {
        $link = $this->generateInstallationLink();
        $title = is_string($title) ? $title : sprintf('Yes, install %s now &#8594;', $this->pluginName);
        return $this->getActionLink($classes, $id, $title, $link);
    }
    
    /**
     * Echoes the anchor tag for the plugin installation link.
     *
     * @param array $classes An array of classes to assign to the anchor, defaults to none.
     * @param null $id The id to assign to the anchor, defaults to none.
     * @param string $title The link title, defaults to 'install plugin-name'.
     *
     * @return void
     */
    public function theInstallationLink(array $classes = null, $id = null, $title = null)
    {
        echo $this->getInstallationLink($classes, $id, $title);
    }
    
    /**
     * Generate an activation URL for a plugin like the ones found in WordPress plugin administration screen.
     *
     * @return string         The plugin activation url
     */
    protected function generateActivationLink()
    {
        
        // the plugin might be located in the plugin folder directly
        
        if (strpos($this->pluginFile, '/')) {
            $pluginFile = str_replace('/', '%2F', $this->pluginFile);
        }
        
        $activateUrl = sprintf(admin_url('plugins.php?action=activate&plugin=%s&plugin_status=all&paged=1&s') , $pluginFile);
        
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
    public function getActivationLink(array $classes = null, $id = null, $title = null)
    {
        $link = $this->generateActivationLink();
        $title = is_string($title) ? $title : sprintf('Yes, activate %s now &#8594;', $this->pluginName);
        return $this->getActionLink($classes, $id, $title, $link);
    }
    
    /**
     * Echoes the anchor tag for the plugin activation link.
     *
     * @param array $classes An array of classes to assign to the anchor, defaults to none.
     * @param null $id The id to assign to the anchor, defaults to none.
     * @param string $title The link title, defaults to 'activate plugin-name'.
     *
     * @return void
     */
    public function theActivationLink(array $classes = null, $id = null, $title = null)
    {
        echo $this->getActivationLink($classes, $id, $title);
    }
    protected function getActionLink(array $classes = null, $id = null, $title, $link)
    {
        $class = is_array($classes) ? sprintf('class="%s"', implode(' ', $classes)) : '';
        $id = is_string($id) ? sprintf('id="%s"', $id) : '';
        return sprintf('<a href="%s" %s %s style="float:right;">%s</a>', $link, $class, $id, $title);
    }
    
    /**
     * Checks if a WordPress plugin is installed.
     *
     * @param  string /null $pluginTitle The plugin title (e.g. "My Plugin"), defautls to plugin name.
     *
     * @return string/boolean       The plugin file/folder relative to the plugins folder path (e.g. "my-plugin/my-plugin.php") or false if the plugin is not installed.
     */
    public function isInstalled($pluginTitle = null)
    {
        $pluginTitle = is_string($pluginTitle) ? $pluginTitle : $this->pluginName;
        
        // get all the plugins
        $installedPlugins = $this->f->get_plugins();
        
        foreach ($installedPlugins as $installedPlugin => $data) {
            
            // check for the plugin title
            if ($data['Title'] == $pluginTitle) {
                
                // return the plugin folder/file
                return $installedPlugin;
            }
        }
        
        return false;
    }
    
    /**
     * Checks if a WordPress plugin is not installed.
     *
     * @param  string /null $pluginTitle The plugin title (e.g. "My Plugin"), defautls to plugin name.
     *
     * @return boolean/string       The plugin file/folder relative to the plugins folder path (e.g. "my-plugin/my-plugin.php") or true if the plugin is not installed.
     */
    public function isNotInstalled($pluginTitle = null)
    {
        $installedPlugin = $this->isInstalled($pluginTitle);
        if ($installedPlugin) {
            return false;
        }
        return true;
    }
    
    /**
     * Adds a plugin to the plugin required plugins list.
     *
     * @param $pluginTitle The required plugin title, e.g. "Hello Dolly".
     * @param $pluginSlug The required plugin slug, e.g. "hello-dolly".
     * @param $pluginUrl The required plugin information url.
     * @param $pluginFile The required plugin main file when installed in WordPress relative to the plugins folder, e.g. "hello-dolly.php" or "my-plugin/plugin.php".
     */
    public function requires($pluginTitle, $pluginSlug, $pluginUrl, $pluginFile)
    {
        $this->requiredPlugins[$pluginTitle] = array(
            'url' => $pluginUrl,
            'file' => $pluginFile,
            'slug' => $pluginSlug
        );
    }
    
    /**
     * Checks for the plugin required plugins and outputs an helpful die message if one is not activated or not installed.
     *
     * @return bool Will return true if all dependencies are satisfied.
     */
    public function checkRequirements()
    {
        $link = false;
        foreach ($this->requiredPlugins as $title => $info) {
            
            // by default ask for installation
            $installing = true;
            $plugin = new tad_Plugin($title, $info['slug'], $info['file']);
            if ($this->isNotInstalled($title)) {
                $link = $plugin->getInstallationLink(null, $info['slug'] . '-installation-link');
            } else if (!$this->f->is_plugin_active($info['file'])) {
                $link = $plugin->getActivationLink(null, $info['slug'] . '-activation-link');
                
                // the plugin needs to be activated, not installed
                $installing = false;
            }
        }
        if ($link) {
            $message = $this->generateWpDieDependencyMessage($info['url'], $title, $link, $installing);
            $title = sprintf("Missing %s prerequisites!", $this->pluginName);
            wp_die($message, $title);
        }
        return true;
    }
    
    /**
     * Generates the message to be displayed in a wp_die generated screen.
     *
     * This is meant to be used to generate a missing plugin dependency wp_die message.
     *
     * @param string $requiredPluginUrl The url to the plugin information page.
     * @param string $requiredPluginTitle The title of the required plugin, e.g. "Hello Dolly".
     * @param string $requiredPluginInstallationOrActivationLink The plugin installation or activation link. See generateInstallationLink and generateActivationLink methods.
     * @param bool $installing If true the required plugin needs to be installed, if false it needs to be activated; defaults to true.
     *
     * @return string The die message markup.
     */
    protected function generateWpDieDependencyMessage($requiredPluginUrl, $requiredPluginTitle, $requiredPluginInstallationOrActivationLink, $installing = true)
    {
        $requiredPluginUrl = "http://wordpress.org/plugins/wp-router/";
        $installedAndOrActivated = $installing ? 'installed and activated' : 'activated';
        $installOrActivate = $installing ? 'install' : 'activate';
        $notice = sprintf('<span style="display:block;text-align:center;">%s requires <a href="%s" target="_blank">%s</a> to be %s. Do you want to %s it now?</span>', $this->pluginName, $requiredPluginUrl, $requiredPluginTitle, $installedAndOrActivated, $installOrActivate);
        $pluginsUrl = admin_url('plugins.php');
        $backToPluginsLink = sprintf('<a href="%s">&#8592; No, get me back to the plugins page</a>', $pluginsUrl);
        $dieMessage = sprintf("%s<br><br>%s%s", $notice, $backToPluginsLink, $requiredPluginInstallationOrActivationLink);
        return $dieMessage;
    }
}

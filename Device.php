<?php

/**
 * @package Device detector
 * @author Iurii Makukh
 * @copyright Copyright (c) 2017, Iurii Makukh
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPL-3.0+
 */

namespace gplcart\modules\device;

use gplcart\core\Module,
    gplcart\core\Library;

/**
 * Main class for Device detector module
 */
class Device extends Module
{

    /**
     * Library class instance
     * @var \gplcart\core\Library
     */
    protected $library;

    /**
     * @param Library $library
     */
    public function __construct(Library $library)
    {
        parent::__construct();

        $this->library = $library;
    }

    /**
     * Implements hook "route.list"
     * @param mixed $routes
     */
    public function hookRouteList(&$routes)
    {
        // Module settings page
        $routes['admin/module/settings/device'] = array(
            'access' => 'module_edit',
            'handlers' => array(
                'controller' => array('gplcart\\modules\\device\\controllers\\Settings', 'editSettings')
            )
        );
    }

    /**
     * Implements hook "theme"
     * @param \gplcart\core\Controller $controller
     */
    public function hookTheme(\gplcart\core\Controller $controller)
    {
        $device = $this->getDevice($controller);
        $store_id = $controller->getStore('store_id');
        $settings = $this->config->module('device');

        if ($controller->isBackend()//
                || $device === 'desktop'//
                || empty($settings['theme'][$store_id][$device])) {
            return null;
        }

        $theme = $settings['theme'][$store_id][$device];

        if ($this->config->isEnabledModule($theme)) {
            $controller->setCurrentTheme($theme);
        }
    }

    /**
     * Returns device type
     * @param \gplcart\core\Controller $controller
     * @return string
     */
    protected function getDevice(\gplcart\core\Controller $controller)
    {
        /* @var $session \gplcart\core\helpers\Session */
        $session = $controller->getProperty('session');

        $device = $session->get('device');

        if (empty($device)) {

            /* @var $detector \Mobile_Detect */
            $detector = $this->getDetectorInstance();

            if ($detector->isMobile()) {
                $device = 'mobile';
            } else if ($detector->isTablet()) {
                $device = 'tablet';
            } else {
                $device = 'desktop';
            }

            $session->set('device', $device);
        }

        return $device;
    }

    /**
     * Returns instance on detector class
     * @return \Mobile_Detect
     * @throws \InvalidArgumentException
     */
    protected function getDetectorInstance()
    {
        $this->library->load('mobile_detect');

        if (!class_exists('Mobile_Detect')) {
            throw new \InvalidArgumentException('Class Mobile_Detect not forund');
        }

        return new \Mobile_Detect;
    }

    /**
     * Implements hook "library.list"
     * @param array $libraries
     */
    public function hookLibraryList(array &$libraries)
    {
        $libraries['mobile_detect'] = array(
            'name' => 'Mobile Detect',
            'description' => 'A lightweight PHP class for detecting mobile devices',
            'url' => 'https://github.com/serbanghita/Mobile-Detect',
            'download' => 'https://github.com/serbanghita/Mobile-Detect/archive/2.8.25.zip',
            'type' => 'php',
            'version_source' => array(
                'file' => 'vendor/mobiledetect/mobiledetectlib/Mobile_Detect.php',
                'pattern' => '/.*VERSION.*(\\d+\\.+\\d+\\.+\\d+)/',
                'lines' => 100
            ),
            'module' => 'device',
            'files' => array(
                'vendor/mobiledetect/mobiledetectlib/Mobile_Detect.php'
            )
        );
    }

    /**
     * Implements hook "module.enable.after"
     */
    public function hookModuleEnableAfter()
    {
        $this->library->clearCache();
    }

    /**
     * Implements hook "module.disable.after"
     */
    public function hookModuleDisableAfter()
    {
        $this->library->clearCache();
    }

    /**
     * Implements hook "module.install.after"
     */
    public function hookModuleInstallAfter()
    {
        $this->library->clearCache();
    }

    /**
     * Implements hook "module.uninstall.after"
     */
    public function hookModuleUninstallAfter()
    {
        $this->library->clearCache();
    }

}

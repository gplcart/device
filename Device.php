<?php

/**
 * @package Device detector
 * @author Iurii Makukh
 * @copyright Copyright (c) 2017, Iurii Makukh
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPL-3.0+
 */

namespace gplcart\modules\device;

use gplcart\core\Module;

/**
 * Main class for Device detector module
 */
class Device extends Module
{

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Implements hook "route.list"
     * @param mixed $routes
     */
    public function hookRouteList(&$routes)
    {
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
    public function hookTheme($controller)
    {
        $device = $this->getDevice();
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
     * @return string
     */
    protected function getDevice()
    {
        /* @var $session \gplcart\core\helpers\Session */
        $session = $this->getHelper('Session');

        $device = $session->get('device');

        if (empty($device)) {

            try {
                $detector = $this->getDetectorInstance();
            } catch (\Exception $ex) {
                return 'desktop';
            }

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
        $this->getLibrary()->load('mobile_detect');

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
        $this->getLibrary()->clearCache();
    }

    /**
     * Implements hook "module.disable.after"
     */
    public function hookModuleDisableAfter()
    {
        $this->getLibrary()->clearCache();
    }

    /**
     * Implements hook "module.install.after"
     */
    public function hookModuleInstallAfter()
    {
        $this->getLibrary()->clearCache();
    }

    /**
     * Implements hook "module.uninstall.after"
     */
    public function hookModuleUninstallAfter()
    {
        $this->getLibrary()->clearCache();
    }

}

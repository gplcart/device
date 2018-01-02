<?php

/**
 * @package Device
 * @author Iurii Makukh
 * @copyright Copyright (c) 2017, Iurii Makukh
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPL-3.0+
 */

namespace gplcart\modules\device;

use Exception;
use gplcart\core\Library,
    gplcart\core\Module as CoreModule;
use gplcart\core\helpers\Session as SessionHelper;
use gplcart\core\exceptions\Dependency as DependencyException;

/**
 * Main class for Device module
 */
class Module
{

    /**
     * Module class instance
     * @var \gplcart\core\Module $module
     */
    protected $module;

    /**
     * Library class instance
     * @var \gplcart\core\Library $library
     */
    protected $library;

    /**
     * Session helper class instance
     * @var \gplcart\core\helpers\Session $session
     */
    protected $session;

    /**
     * @param CoreModule $module
     * @param Library $library
     * @param SessionHelper $session
     */
    public function __construct(CoreModule $module, Library $library, SessionHelper $session)
    {
        $this->module = $module;
        $this->library = $library;
        $this->session = $session;
    }

    /**
     * Implements hook "library.list"
     * @param array $libraries
     */
    public function hookLibraryList(array &$libraries)
    {
        $libraries['mobile_detect'] = array(
            'name' => /* @text */'Mobile Detect',
            'description' => /* @text */'A lightweight PHP class for detecting mobile devices',
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
     * Implements hook "route.list"
     * @param array $routes
     */
    public function hookRouteList(array &$routes)
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
        $this->switchTheme($controller);
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

    /**
     * Returns a device type
     * @return string
     */
    public function getDeviceType()
    {
        $device = $this->session->get('device');

        if (empty($device)) {

            try {
                $detector = $this->getLibrary();
            } catch (Exception $ex) {
                return 'desktop';
            }

            if ($detector->isMobile()) {
                $device = 'mobile';
            } else if ($detector->isTablet()) {
                $device = 'tablet';
            } else {
                $device = 'desktop';
            }

            $this->session->set('device', $device);
        }

        return $device;
    }

    /**
     * Returns the mobile detector instance
     * @return \Mobile_Detect
     * @throws DependencyException
     */
    public function getLibrary()
    {
        $this->library->load('mobile_detect');

        if (class_exists('Mobile_Detect')) {
            return new \Mobile_Detect;
        }

        throw new DependencyException('Class Mobile_Detect not forund');
    }

    /**
     * Switch the current theme
     * @param \gplcart\core\Controller $controller
     * @return bool
     */
    protected function switchTheme($controller)
    {
        if ($controller->isInternalRoute()) {
            return false;
        }

        $device = $this->getDeviceType();
        $store_id = $controller->getStoreId();
        $settings = $this->module->getSettings('device');

        if ($controller->isBackend() || $device === 'desktop'//
                || empty($settings['theme'][$store_id][$device])) {
            return false;
        }

        $theme = $settings['theme'][$store_id][$device];

        if ($this->module->isEnabled($theme)) {
            $controller->setCurrentTheme($theme);
            return true;
        }

        return false;
    }

}

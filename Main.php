<?php

/**
 * @package Device
 * @author Iurii Makukh
 * @copyright Copyright (c) 2017, Iurii Makukh
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPL-3.0+
 */

namespace gplcart\modules\device;

use Exception;
use gplcart\core\Controller;
use gplcart\core\helpers\Session;
use gplcart\core\Library;
use gplcart\core\Module;
use LogicException;

/**
 * Main class for Device module
 */
class Main
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
     * @param Module $module
     * @param Library $library
     * @param Session $session
     */
    public function __construct(Module $module, Library $library, Session $session)
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
            'name' => 'Mobile Detect', // @text
            'description' => 'A lightweight PHP class for detecting mobile devices', // @text
            'url' => 'https://github.com/serbanghita/Mobile-Detect',
            'download' => 'https://github.com/serbanghita/Mobile-Detect/archive/2.8.25.zip',
            'type' => 'php',
            'version' => '2.8.25',
            'module' => 'device',
            'vendor' => 'mobiledetect/mobiledetectlib'
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
     * @param Controller $controller
     */
    public function hookTheme(Controller $controller)
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

            $detector = $this->getLibrary();

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
     * @throws LogicException
     */
    public function getLibrary()
    {
        $this->library->load('mobile_detect');

        if (class_exists('Mobile_Detect')) {
            return new \Mobile_Detect;
        }

        throw new LogicException('Class Mobile_Detect not found');
    }

    /**
     * Switch the current theme
     * @param Controller $controller
     */
    protected function switchTheme(Controller $controller)
    {
        if (!$controller->isInternalRoute()) {

            try {

                $device = $this->getDeviceType();
                $store_id = $controller->getStoreId();
                $settings = $this->module->getSettings('device');

                if (!$controller->isBackend() && $device !== 'desktop' && !empty($settings['theme'][$store_id][$device])) {
                    $theme = $settings['theme'][$store_id][$device];
                    if ($this->module->isEnabled($theme)) {
                        $controller->setCurrentTheme($theme);
                    }
                }

            } catch (Exception $ex) {
                trigger_error($ex->getMessage());
            }
        }
    }

}

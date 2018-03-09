[![Build Status](https://scrutinizer-ci.com/g/gplcart/device/badges/build.png?b=master)](https://scrutinizer-ci.com/g/gplcart/device/build-status/master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/gplcart/device/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/gplcart/device/?branch=master)

Device Detector is a [GPL Cart](https://github.com/gplcart/gplcart) module that allows to detect user's device type (mobile, tablet) and switch to the corresponding theme. Based on [Mobile Detect](https://github.com/serbanghita/Mobile-Detect) library

**Features**

- Defines user's device type and stores it in the user session
- Optionally switches to a specific theme
- Provides Mobile Detect library for other modules

**Installation**

This module requires 3-d party library which should be downloaded separately. You have to use [Composer](https://getcomposer.org) to download all the dependencies.

1. From your web root directory: `composer require gplcart/device`. If the module was downloaded and placed into `system/modules` manually, run `composer update` to make sure that all 3-d party files are presented in the `vendor` directory.
2. Go to `admin/module/list` end enable the module
3. Adjust settings on `admin/module/settings/device`

To get stored device type in your custom module: $_SESSION['device']. Available values are: desktop, mobile, tablet
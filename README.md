[![Build Status](https://scrutinizer-ci.com/g/gplcart/device/badges/build.png?b=master)](https://scrutinizer-ci.com/g/gplcart/device/build-status/master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/gplcart/device/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/gplcart/device/?branch=master)

Device Detector is a [GPL Cart](https://github.com/gplcart/gplcart) module that allows to detect user's device type (mobile, tablet) and switch to the corresponding theme. Based on [Mobile Detect](https://github.com/serbanghita/Mobile-Detect) library

**Features**

- Defines user's device type and stores it in the user session
- Optionally switches to a specific theme
- Provides Mobile Detect library for other modules

**Installation**

1. Download and extract to `system/modules` manually or using composer `composer require gplcart/device`. IMPORTANT: If you downloaded the module manually, be sure that the name of extracted module folder doesn't contain a branch/version suffix, e.g `-master`. Rename if needed.
2. Go to `admin/module/list` end enable the module
3. Adjust settings on `admin/module/settings/device`

To get stored device type in your custom module: $_SESSION['device']. Available values are: desktop, mobile, tablet
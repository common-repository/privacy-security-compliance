<?php

require_once dirname(__FILE__) . '/essert-config.php';
require_once dirname(__FILE__) . '/___epc_classes/App.php';
require_once dirname(__FILE__) . '/___epc_classes/Hooks.php';

use ___epc_classes\App;
use ___epc_classes\Hooks;

/**
 * Plugin Name: Privacy & Security Compliance
 * 
 * Description: Essert Privacy Compliance plugin assists businesses and webmasters to  comply with California Consumer Privacy Act (CCPA), CPRA, GDPR, POPI Act, Virginia CDPA, Colorado Privacy act, Utah privacy law, Connecticut privacy law, and more. It creates a privacy request intake form and Do Not Sell My Personal Information Button. It also generates privacy policy, web site terms of use, cookie policy, and more. This plugin in free. You could manage and automate your requests with a web application. Sign Up at essert.io. We provide basic privacy compliance at no cost.
 * 
 * Version: 1.3
 * Author: Essert Inc.
 * Author URI: https://essert.io/
 * Plugin URI: https://essert.io/essert-privacy-compliance-automation/
 * License: Expat license
 * License URI: https://directory.fsf.org/wiki/License:Expat
 * Text Domain: Essert.io
 * Cookie Notice: Essert does not track users without permission. Such permission is explicitly obtained
 * Copyright (C) 2022-, essert.io support@essert.io
 * 
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy 
 * of this software and associated documentation files (the "Software"), to deal 
 * in the Software without restriction, including without limitation the rights 
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies 
 * of the Software, and to permit persons to whom the Software is furnished to 
 * do so, subject to the following conditions:

 * The above copyright notice and this permission notice shall be included in all 
 * copies or substantial portions of the Software.

 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR 
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, 
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL
 * THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER 
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, 
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE. 

 */


// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

function __essert_privacy_compliance_app_init()
{
    add_action('track_event', [Hooks::class, '_track_event']);
    add_action('plugins_loaded', [App::class, 'init']);
    register_activation_hook(__FILE__, [Hooks::class, 'register_activation_hook']);
    register_deactivation_hook(__FILE__, [Hooks::class, 'register_deactivation_hook']);
    register_uninstall_hook(__FILE__, [Hooks::class, 'register_uninstall_hook']);
}

__essert_privacy_compliance_app_init();

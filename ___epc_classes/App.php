<?php

namespace ___epc_classes;

require_once dirname(__FILE__) . '/Options.php';
require_once dirname(__FILE__) . '/AjaxController.php';

use ___epc_classes\Options;
use ___epc_classes\AjaxController;

class App
{

    private $baseUrl;

    function __construct()
    {
        ob_start();
        $this->baseUrl = 'https://api.essert.io/infosec/max';
        if (!wp_doing_ajax()) {
            if (is_admin()) {
                add_action('admin_menu', [$this, 'plugin_menu']);
            } else {
                add_shortcode('EssertAuthApp', [$this, 'EssertAuthApp']);
                add_shortcode('EssertBudgetingApp', [$this, 'EssertBudgetingApp']);
                add_shortcode('EssertPolicyDocs', [$this, 'essert_policy_docs']);
                add_shortcode('EssertDsarRequestForm', [$this, 'EssertDsarRequestForm']);
                add_shortcode('EssertDNSButton', [$this, 'EssertDNSButton']);
            }
        } else $this->bindAjaxCalls();
        ob_clean();
    }

    public function __call($name, $args)
    {
        $event_data = ["Short Code" => $name];
        $element = "<div id='EssertAppRenderEl'></div>";
        $is_shortcode = true;
        $vars = [
            'env' => 'production',
            'base_url' => $this->baseUrl,
            'ajax_url' => admin_url('admin-ajax.php'),
            'source' => 'ext',
            'app_settings' => Options::getSettings(),
            'nonce' => wp_create_nonce('ajax-nonce'),
            'user_type' => ESSERT_APP_USER_TYPE
        ];
        wp_enqueue_script('essert-script', plugins_url('../js/app.js', __FILE__), [], false, true);
        switch ($name) {
            case 'AdminPluginMenu':
                $vars['dns_button_settings'] = Options::getDnsButtonSettings();
                $is_shortcode = false;
                do_action('track_event', ['event' => "PLUGIN:ADMIN MENU LOADED", 'props' => $event_data]);
                break;
            case 'EssertAuthApp':
            case 'EssertBudgetingApp':
            case 'EssertDsarRequestForm':
            case 'EssertDNSButton':
                $vars['short_code_name'] = $name;
                if (in_array($name, ['EssertDsarRequestForm', 'EssertDNSButton'])) {
                    $vars['dns_button_settings'] = Options::getDnsButtonSettings();
                    if (isset($args[0]['_type'])) {
                        $event_data["Form Type"] = $args[0]['_type'];
                        if ($args[0]['_type'] === 'ccpa') {
                            $vars['short_code_name'] = 'EssertDsarCcpaRequestForm';
                        } else if ($args[0]['_type'] === 'ccpa-dns') {
                            $vars['short_code_name'] = 'EssertDsarCcpaDnsRequestForm';
                        } else {
                            $vars['short_code_name'] = 'EssertDsarPopiRequestForm';
                        }
                    }
                }
                do_action('track_event', ['event' => "PLUGIN:SHORTCODE LOADED:{$name}", 'props' => $event_data]);
                break;
        }

        if (isset($args[0]) && gettype($args[0]) === "array") {
            $vars = array_merge($vars, ["short_code_args" => $args[0]]);
        }

        wp_localize_script('essert-script', 'vars', $vars);
        if ($is_shortcode) {
            return wp_kses_post($element);
        }
        echo wp_kses_post($element);
    }

    static function init()
    {
        new static();
    }

    function plugin_menu()
    {
        add_menu_page('Essert Privacy Compliance', 'Essert', 'manage_options', 'essert-privacy-compliance', [$this, 'AdminPluginMenu'], plugins_url(ESSERT_PLUGIN_DIR_NAME . '/images/icon.png'), 50);
    }

    function essert_policy_docs($args = [])
    {
        $appSettings = Options::getSettings();
        $secret_key = isset($appSettings['SecretKey']) ? $appSettings['SecretKey'] : '';
        $response = wp_remote_get("{$this->baseUrl}/public/policy/fetch?__key__={$secret_key}&macro={$args['_sc']}");
        do_action('track_event', ['event' => "PLUGIN:SHORTCODE LOADED:EssertPolicyDocs", 'props' => [
            "Short Code" => "EssertPolicyDocs",
            'Policy Name' => $args['_sc']
        ]]);
        return wp_kses_post(wp_remote_retrieve_body($response));
    }

    private function bindAjaxCalls()
    {
        //App Setting
        if (is_admin()) {
            add_action("wp_ajax_essert_budgeting_update", [AjaxController::class, 'essert_budgeting_update']);
            add_action("wp_ajax_essert_my_account_update", [AjaxController::class, 'my_account_update']);
            add_action("wp_ajax_essert_dns_button_settings_update", [AjaxController::class, 'essert_dns_button_settings_update']);
        }
    }
}

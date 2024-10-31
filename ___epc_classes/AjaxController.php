<?php

namespace ___epc_classes;

require_once dirname(__FILE__) . '/Options.php';

use ___epc_classes\Options;

class AjaxController
{

    public static function __callStatic($name, $arguments)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            if (!wp_verify_nonce($_REQUEST['nonce'], 'ajax-nonce')) {
                wp_send_json_error('Unauthorised!', 401);
            }

            $request = json_decode(file_get_contents('php://input'), true);

            switch ($name) {
                case 'essert_budgeting_update':
                    $contactURL = '';
                    $signupURL = '';
                    if (isset($request['ContactURL'])) {
                        $contactURL = sanitize_url($request['ContactURL']);
                    }
                    if (isset($request['SignupURL'])) {
                        $signupURL = sanitize_url($request['SignupURL']);
                    }
                    $settings = [
                        'ContactURL' => $contactURL,
                        'SignupURL' => $signupURL,
                    ];
                    Options::updateSettings($settings);
                    do_action('track_event', ['event' => "PLUGIN:BUDGETING SETTINGS UPDATE", 'props' => $settings]);
                    break;
                case 'my_account_update':
                    $secretKey = '';
                    if (isset($request['SecretKey'])) {
                        $secretKey = sanitize_text_field($request['SecretKey']);
                    }
                    $settings = [
                        'SecretKey' => $secretKey,
                    ];
                    Options::updateSettings($settings);
                    do_action('track_event', ['event' => "PLUGIN:SECRET KEY UPDATE", 'props' => $settings]);
                    break;
                case 'essert_dns_button_settings_update':
                    $data = [
                        'alignment' => isset($request['alignment']) ? sanitize_text_field($request['alignment']) : '',
                        'backgroundColor' => isset($request['backgroundColor']) ? sanitize_text_field($request['backgroundColor']) : '',
                        'borderRadius' => isset($request['borderRadius']) ? intval(sanitize_text_field($request['borderRadius'])) : 0,
                        'buttonText' => isset($request['buttonText']) ? sanitize_text_field($request['buttonText']) : '',
                        'color' => isset($request['color']) ? sanitize_text_field($request['color']) : '',
                        'fontSize' => isset($request['fontSize']) ? intval(sanitize_text_field($request['fontSize'])) : 0,
                        'paddingBottom' => isset($request['paddingBottom']) ? intval(sanitize_text_field($request['paddingBottom'])) : 0,
                        'paddingLeft' => isset($request['paddingLeft']) ? intval(sanitize_text_field($request['paddingLeft'])) : 0,
                        'paddingRight' => isset($request['paddingRight']) ? intval(sanitize_text_field($request['paddingRight'])) : 0,
                        'paddingTop' => isset($request['paddingTop']) ? intval(sanitize_text_field($request['paddingTop'])) : 0,
                        'right' => isset($request['right']) ? intval(sanitize_text_field($request['right'])) : 0,
                        'top' => isset($request['top']) ? intval(sanitize_text_field($request['top'])) : 0,
                        'zIndex' => isset($request['zIndex']) ? intval(sanitize_text_field($request['zIndex'])) : 1,
                        'pageLink' => isset($request['pageLink']) ? sanitize_text_field($request['pageLink']) : '',
                        'placement' => isset($request['placement']) ? sanitize_text_field($request['placement']) : 'Auto',
                    ];
                    Options::updateDnsButtonSettings($data);
                    do_action('track_event', ['event' => "PLUGIN:DNS BUTTON SETTINGS UPDATE"]);
                    break;
            }
        }
    }
}

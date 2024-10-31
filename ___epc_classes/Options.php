<?php

namespace ___epc_classes;

abstract class Options
{
    static $options_list = [
        ESSERT_SETTINGS_OPTION,
        ESSERT_DNS_BUTTON_SETTINGS_OPTION
    ];

    static function getSettings()
    {
        return get_option(ESSERT_SETTINGS_OPTION);
    }

    static function updateSettings($value = [], $merge = true)
    {
        update_option(ESSERT_SETTINGS_OPTION, $merge ? array_merge(static::getSettings(), $value) : $value);
    }

    static function getDnsButtonSettings()
    {
        return get_option(ESSERT_DNS_BUTTON_SETTINGS_OPTION);
    }

    static function updateDnsButtonSettings($value = [], $merge = true)
    {
        update_option(ESSERT_DNS_BUTTON_SETTINGS_OPTION, $merge ? array_merge(static::getDnsButtonSettings(), $value) : $value);
    }

    static function setOptions()
    {
        foreach (static::$options_list as $option) {
            if (!get_option($option)) {
                add_option($option, []);
            }
        }
    }

    static function deleteOptions()
    {
        foreach (static::$options_list as $option) {
            delete_option($option);
        }
    }
}

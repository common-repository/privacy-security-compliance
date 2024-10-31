import axios from "axios"
import { EssertPrivacyComplianceApp } from "essert.privacy.compliance.app"
window.$WpEssertAjax = axios
window.$WpEssertAjax.defaults.headers.common['Accept'] = '*/*';
import {
    updateBudgetingSettings,
    updateMyAccountSettings,
    updateDnsButtonSettings
} from "./actions"
const wpContentEl = document.querySelector('#wpcontent')
if (wpContentEl) {
    wpContentEl.style.paddingLeft = '0px'
}
const shortCodeName = vars?.short_code_name ?? null
const renderEl = document.querySelector('#EssertAppRenderEl')

const app = new EssertPrivacyComplianceApp(renderEl, {
    userType: vars.user_type,
    source: vars.source,
    shortCodeName,
    shortCodeArgs: vars?.short_code_args ?? null,
    events: {
        onReady: () => {
            app.dispatch("APP_SETTINGS", vars.app_settings)
            if (!vars.short_code_name || ['EssertDsarRequestForm', 'EssertDNSButton'].includes(vars.short_code_name)) {
                app.dispatch('DNS_BUTTON_SETTINGS', vars.dns_button_settings)
            }
        },
        onReceiveData: ({ type, data = null }) => {
            switch (type) {
                case "BUDGETING_SETTINGS_SUBMIT":
                    updateBudgetingSettings(app, data)
                    break
                case "MY_ACCOUNT_SETTINGS_SUBMIT":
                    updateMyAccountSettings(app, data)
                    break
                case "DNS_BUTTON_SETTINGS_SUBMIT":
                    updateDnsButtonSettings(app, data)
                    break
            }
        }
    }
}, vars.env)

$WpEssertAjax.interceptors.request.use(config => {
    if (config?.url?.includes('wp-admin/admin-ajax.php') && config?.method === 'post') {
        config.params['nonce'] = vars.nonce
    }
    app.dispatch('LOADING', true)
    return config;
}, error => {
    app.dispatch('LOADING', false)
    app.dispatch('NOTIFY_ERROR', 'Invalid Request!')
    return Promise.reject(error)
});

$WpEssertAjax.interceptors.response.use(response => {
    app.dispatch('LOADING', false)
    return response;
}, error => {
    app.dispatch('LOADING', false)
    app.dispatch('NOTIFY_ERROR', 'Request Failed!')
    return Promise.reject(error)
});
export async function updateBudgetingSettings(app, payload) {
    const { ContactURL = null, SignupURL = null } = payload
    await $WpEssertAjax.post(`${vars.ajax_url}`, { ContactURL, SignupURL },
        { params: { action: 'essert_budgeting_update' } })
    app.dispatch("APP_SETTINGS", { ...vars.app_settings, ContactURL, SignupURL })
    app.dispatch('NOTIFY_SUCCESS', 'Settings Updated!')
}

export async function updateMyAccountSettings(app, payload) {
    const { SecretKey = null } = payload
    await $WpEssertAjax.post(`${vars.ajax_url}`, { SecretKey },
        { params: { action: 'essert_my_account_update' } })
    app.dispatch("APP_SETTINGS", { ...vars.app_settings, SecretKey })
    app.dispatch('NOTIFY_SUCCESS', 'Settings Updated!')
}

export async function updateDnsButtonSettings(app, payload) {
    await $WpEssertAjax.post(`${vars.ajax_url}`, payload,
        { params: { action: 'essert_dns_button_settings_update' } })
    app.dispatch("DNS_BUTTON_SETTINGS", payload)
    app.dispatch('NOTIFY_SUCCESS', 'Settings Updated!')
}

==============================================================
GRAPHQL API
==============================================================


==============================================================
Store configuration
==============================================================

{
    storeConfig {
        callforprice_general_active
        callforprice_general_notification_type
        callforprice_general_enablefor
        callforprice_general_display_categories
        callforprice_general_typeas
        callforprice_general_displayed_text
        callforprice_general_displayed_text_label
        callforprice_general_label_color
        callforprice_general_allow_customer_group
        callforprice_general_display_customer_group
        callforprice_general_header_text
        callforprice_privacy_setting_privacy_enable
        callforprice_privacy_setting_privacy_text
        callforprice_privacy_setting_show_privacy_policy
        callforprice_recaptcha_setting_recaptcha_enable
        callforprice_recaptcha_setting_sitekey
        callforprice_recaptcha_setting_secret_key
    }
}


mutation {
    callforpriceInquiry(
        input:{
			cname: "hello"
			email:"asdasd@gmail.com"
			country_id:"IN"
			phone_number:"+918141102201"
			comment:"hello test comment"
            privacy:0
			g_recaptcha_response:"dadaddaddad"
            callProductId:1
        }
    ){
        success
        successmsg
        errormsg
    }
}

{
    "data": {
        "callforpriceInquiry": {
            "success": "1",
            "successmsg": "Your inquiry submitted successfully",
            "errormsg": ""
        }
    }
}


==============================================================

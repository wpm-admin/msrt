<?php
// Heading
$_['heading_title'] = 'Elavon Converge EU Gateway';
$_['text_extension'] = 'Extensions';
$_['text_converge_gateway_eu'] = '<a href="https://www.elavon.com" target="_blank"><img alt="Converge Logo" src="view/image/payment/logo_converge.png"></a>';
$_['text_edit'] = 'Edit Elavon Converge EU Gateway';

$_['text_sandbox'] = 'Sandbox';
$_['text_production'] = 'Production';
/* translators: %1$s: plugin title */
$_['text_success'] = 'Success: You have modified %1$s settings!';
$_['text_dynamic_descriptor_settings_description'] = "Your dynamic descriptor settings affect what appears on you customer's credit card statement.";
$_['text_proxy_settings_description'] = "If your system uses a proxy server to establish the connection between OpenCart and Converge, set API Uses Proxy to “Yes” and complete the Proxy Host and Proxy Port fields.";
$_['text_terminal_submit'] = 'Run Setup';
$_['text_plugin_desc'] = 'Receive credit card payments using Elavon Converge EU Gateway.';
/* translators: %1$s: plugin version */
$_['text_plugin_version'] = 'Plugin version: %1$s';
/* translators: %1$s: API SDK version */
$_['text_sdk_version'] = 'API SDK version: %1$s';
$_['text_authorize_and_immediate_capture'] = 'Authorize And Immediate Capture';
$_['text_authorize_and_delayed_capture'] = 'Authorize And Delayed Capture';
$_['text_hpp'] = 'HPP (PCI SAQ A)';
$_['text_lightbox'] = 'Lightbox (PCI SAQ A)';
$_['default_plugin_title'] = 'Elavon Converge EU Gateway';
$_['text_dialog_capture'] = 'Are you sure you want to capture this transaction?';
$_['text_dialog_refund'] = 'Are you sure you want to refund this transaction?';
$_['text_order_not_found'] = "Order not found in the collection";
$_['text_capture_failed'] = "Capture failed.";
$_['text_refund_failed'] = "Refund failed.";
$_['text_void_failed'] = "Void failed.";
$_['text_dialog_void'] = 'Are you sure you want to void this transaction?';
$_['text_full_refund_success'] = 'You have successfully refunded this order for full amount';
$_['text_partial_refund_success'] = 'You have successfully refunded [refund_amount] [refund_currency]';
$_['text_refund_exceeded_amount'] = 'Amount entered for refund is exceeding total amount available to refund.';
$_['text_refund_invalid_amount'] = 'Amount entered for refund is invalid.';
$_['text_refund_required_amount'] = 'Please enter Refund Amount.';
$_['text_capture_success'] = 'Captured amount of [captured_amount] [captured_currency].';
$_['text_ok'] = 'OK';
$_['text_cancel'] = 'Cancel';
$_['text_bad_order_or_transaction_id'] = 'Bad order or transaction ID.';

/* translators: %1$s: amount, %2$s: currency code, %3$s: transaction id */
$_['text_voided_amount_for_transaction'] = 'Voided amount of %1$s %2$s. Transaction id: %3$s';

/* translators: %1$s: amount, %2$s: currency code, %3$s: transaction id */
$_['text_captured_amount_for_transaction'] = 'Captured amount of %1$s %2$s. Transaction id: %3$s';

$_['entry_gateway_region'] = 'Gateway Region:';
$_['entry_debug_log'] = 'Debug Log';
$_['entry_debug_log_desc'] = 'Log Converge events inside System > Maintenance > Error Logs. Use only for development purpose.';
$_['entry_public_key'] = 'Public Key';
$_['entry_public_key_desc'] = 'The public key for your account, provided by Converge.';
$_['entry_secret_key'] = 'Secret Key';
$_['entry_secret_key_desc'] = 'The secret key for your account, provided by Converge.';
$_['entry_user_id'] = 'User ID:';
$_['entry_user_pin'] = 'User PIN:';
$_['entry_environment'] = 'Environment';
$_['entry_environment_desc'] = 'Choose environment.';
$_['entry_status'] = 'Enabled';
$_['entry_status_desc'] = 'Enable Elavon Converge EU Gateway.';
$_['entry_title'] = 'Title';
$_['entry_title_desc'] = 'Payment method title that the customer will see during checkout.';
$_['entry_action'] = 'Payment Action:';
$_['entry_integration_option'] = 'Integration Option';
$_['entry_integration_option_desc'] = 'Choose the integration option.';
$_['entry_region'] = 'Region:';
$_['entry_accepted_types'] = 'Accepted Payments:';
$_['entry_cc_accepted'] = "Accepted CC's:";
$_['entry_wallets_enabled'] = 'Wallets Enabled:';
$_['entry_language'] = 'Language Translations:';
$_['entry_value_add_service'] = 'Value Add Service:';
$_['entry_license_text'] = 'License Text:';
$_['entry_processor_account_id'] = 'Processor Account Id';
$_['entry_processor_account_id_desc'] = 'The processor account ID is used to identify the Merchant when connecting to Converge.';
$_['entry_merchant_name'] = 'Merchant Name';
$_['entry_merchant_name_desc'] = "The Merchant Name represents the Merchant 'Doing Business As' (DBA) name and is automatically filled based on the Processor Account Id.";
$_['entry_merchant_alias'] = 'Merchant Alias';
$_['entry_merchant_alias_desc'] = "The merchant alias is an unique ID that acts a username for authentication.";
$_['entry_payment_action'] = 'Payment Action';
$_['entry_payment_action_desc'] = "Choose whether you wish to capture fund immediately or authorize payment only.";
$_['entry_payment_converge_mail'] = 'Converge Email';
$_['entry_payment_converge_mail_desc'] = "Choose if Converge should send emails to the customer.";
$_['entry_license_code'] = 'License Code';
$_['entry_descriptor_name'] = 'Name';
$_['entry_descriptor_name_description'] = "The value in the business name field of a customer's statement.";
$_['entry_descriptor_phone'] = 'Phone';
$_['entry_descriptor_phone_description'] = "The value in the phone number field of a customer's statement.";
$_['entry_descriptor_url'] = 'URL';
$_['entry_descriptor_url_description'] = "The value in the URL/web address field of a customer's statement.";
$_['entry_proxy_host'] = 'Proxy Host';
$_['entry_proxy_port'] = 'Proxy Port';
$_['entry_save_payment_method'] = 'Enable Saved Payment Methods';
$_['entry_save_payment_method_description'] = "If enabled, the already saved payment methods and a checkbox for saving new payment methods will be available on the checkout page.";
$_['entry_save_for_later_use_message'] = 'Save for Later Use Message';
$_['entry_save_for_later_use_message_description'] = "This message will be displayed to the shopper next to the Save for later use option.";
$_['entry_save_for_later_use_message_default'] = "By placing your order, you agree with your card details being saved.";
$_['entry_api_uses_proxy'] = "API Uses Proxy";


$_['tab_advanced'] = 'Advanced Settings';
$_['tab_dynamic_descriptors_settings'] = 'Dynamic Descriptors Settings';
$_['tab_proxy_settings'] = 'Proxy Settings';
$_['text_payment_info'] = 'Payment Information';

$_['text_transaction_status'] = 'Converge transaction status';
$_['text_transaction_id'] = 'Transaction ID';
$_['text_transaction_type'] = 'Transaction Type';
$_['text_transaction_createdAt'] = 'Date Added';
$_['text_merchant_name'] = 'Merchant Name';
$_['text_transaction_amount'] = 'Transaction Amount';
$_['text_transaction_description'] = 'Transaction Description';
$_['text_merchant_transaction_code'] = 'Merchant Transaction Code';

$_['text_transaction_history_title'] = 'Transaction History';
$_['text_transaction_date'] = 'Transaction Date';

$_['text_transaction_order'] = 'Converge Order';

$_['text_transaction_action_title'] = 'Actions';
$_['text_transaction_void'] = 'Void';
$_['na'] = 'N/A';
$_['text_transaction_refund'] = 'Refund';
$_['text_transaction_refund_placeholder'] = 'Refund Amount';
$_['text_transaction_capture'] = 'Capture';

$_['error_status_required'] = 'Status field is required.';
$_['error_processor_account_id_required'] = 'The Processor Account Id field is required.';
$_['error_merchant_alias_required'] = 'The Merchant Alias field is required.';
$_['error_public_key_required'] = 'Public Key field is required.';
$_['error_public_key_numeric'] = 'Public Key field must be numeric.';
$_['error_secret_key_required'] = 'Secret Key field is required.';
$_['error_save_for_later_use_message_required'] = 'The Save for later use message field is required.';
$_['error_invalid_public_key_or_merchant_alias'] = 'Invalid public key or merchant alias.';
$_['error_invalid_secret_key_or_merchant_alias'] = 'Invalid secret key or merchant alias.';
$_['error_invalid_processor_account_id'] = 'Invalid processor account id.';
$_['error_updating_history'] = 'There was an error while updating order history.';
$_['error_connection_unsuccessful'] = 'The configuration fields could not be validated due to unsuccessful connection to the Converge API.';
$_['error_maxlength'] = 'The field cannot be longer than %1$s characters.';
$_['error_delete_alert_message'] = 'If you change the account, the stored shoppers and cards will be deleted. Are you sure you want to do this?';

/* translators: %1$s: already translated field name, %2$s: a number */
$_['maxlength_error_message'] = 'The field %1$s cannot be longer than %2$s characters.';

/* translators: %1$s: already translated field name, %2$s: a list of special characters */
$_['basic_safe_string_error_message'] = 'The field %1$s does not allow the following special characters: %2$s';

/* translators: %1$s: already translated field name, %2$s: a list of special characters */
$_['phone_safe_string_error_message'] = 'The field %1$s allows only these special characters: %2$s including space.';

$_['text_there_are_errors'] = 'Some fields are invalid. Please check all tabs for error messages.';
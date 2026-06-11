<?php

$_['text_title'] = 'Elavon Converge EU Gateway';

$_['text_plugin_desc'] = 'Pay securely using Elavon Converge.';

/* translators: %1$s: amount, %2$s: currency code */
$_['text_cancel_order_status'] = 'Order of amount %1$s %2$s is canceled by user request.';

/* translators: %1$s: amount, %2$s: currency code, %3$s: transaction id */
$_['text_processing_order_status'] = 'Converge transaction created with amount: %1$s %2$s and transaction id: %3$s';

/* translators: %1$s: transaction id, %2$s: amount, %3$s: currency code */
$_['text_failed_order_status'] = 'Transaction %1$s, with the amount of %2$s %3$s has failed';

$_['hpp_button_confirm'] = 'Place order';

/* translators: %1$s: integration method */
$_['order_created_pending'] = 'Initial order creation via %1$s';

$_['text_converge_api_error'] = 'There is a problem with Converge2 API.';

/* translators: %1$s: converge_error id */
$_['text_failed_creating_transaction'] = 'There was an error creating the transaction. Converge error: %1$s';

/* translators: %1$s: converge_error id */
$_['text_failed_creating_stored_card'] = 'There was an error creating the stored card. Converge error: %1$s';
$_['text_stored_card_already_exists_error'] = 'The credit card number could not be saved because the stored card already exists.';
$_['text_stored_card_could_not_be_saved_error'] = 'The credit card number could not be saved.';

$_['text_error_processing_payment'] = 'There was an error processing your payment.';
$_['text_save_for_later_use'] = 'Save for later use';
$_['text_use_new_card'] = 'Use new card';
$_['text_delete'] = 'Delete';
$_['text_stored_card_error_invalid_request'] = 'Invalid stored card request.';
$_['text_stored_card_error_delete'] = 'Payment method cannot be deleted. Please contact the merchant.';
$_['text_stored_card_confirm_delete_message'] = 'Are you sure you want to delete this payment method?';
$_['text_stored_card_delete_success_message'] = 'Payment Method was successfully removed.';

/* translators: %1$s: card type (e.g. Visa),  %2$s: last 4 digits from card number (e.g. 4000), %3$s: expiry month (mm), %4$s: expiry year (yy) */
$_['text_stored_card_expire'] = '%1$s ending in %2$s (expires %3$s/%4$s)';

/* translators: %1$s: card type (e.g. Visa),  %2$s: last 4 digits from card number (e.g. 4000), %3$s: expiry month (mm), %4$s: expiry year (yy) */
$_['text_stored_card_expired'] = '%1$s ending in %2$s <span class="text-danger">(expired %3$s/%4$s)</span>';

/* translators: %1$s: already translated field name */
$_['required_error_message'] = 'The field %1$s is required.';

/* translators: %1$s: already translated field name, %2$s: a number */
$_['maxlength_error_message'] = 'The field %1$s cannot be longer than %2$s characters.';

/* translators: %1$s: already translated field name, %2$s: a list of special characters */
$_['basic_safe_string_error_message'] = 'The field %1$s does not allow the following special characters: %2$s';

/* translators: %1$s: already translated field name, %2$s: a list of special characters */
$_['phone_safe_string_error_message'] = 'The field %1$s allows only these special characters: %2$s including space.';

$_['converge_down_error_message'] = 'The selected payment method is not available. Please try again later.';
$_['zero_amount_error_message'] = 'This transaction cannot be processed because the amount to be charged is zero. Please contact the merchant.';

/* translators: %1$s: number of order items */
$_['max_order_items_error_message'] = 'The number of cart items should be less than or equal to %1$s (including shipping and tax items).';
$_['text_browser_error'] = 'Your browser is not supported or the browser version is outdated. In order to enjoy the full shopping experience, we recommend using the latest version of Chrome, Firefox or Safari.';

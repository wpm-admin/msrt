<?php
// Heading
$_['heading_title']					= 'Realex Redirect';

// Text
$_['text_success']					= 'F&eacute;licitations, vous avez modifi&eacute; le compte Realex avec succ&egrave;s !';
$_['text_edit']                     = 'Modifier Realex Redirect';
$_['text_live']						= 'R&eacute;&eacute;l';
$_['text_demo']						= 'Test';
$_['text_card_type']				= 'Type carte';
$_['text_enabled']					= 'Activ&eacute;';
$_['text_use_default']				= 'Utiliser par d&eacute;faut';
$_['text_merchant_id']				= 'Identifiant du marchand';
$_['text_subaccount']				= 'Sous-compte';
$_['text_secret']					= 'Mot secret';
$_['text_card_visa']				= 'Visa';
$_['text_card_master']				= 'Mastercard';
$_['text_card_amex']				= 'American Express';
$_['text_card_switch']				= 'Switch/Maestro';
$_['text_card_laser']				= 'Laser';
$_['text_card_diners']				= 'Diners';
$_['text_capture_ok']				= 'La capture a &eacute;t&eacute; un succ&egrave;s';
$_['text_capture_ok_order']			= 'La capture a &eacute;t&eacute; un succ&egrave;s, le statut de la commande est pass&eacute; &agrave; succ&egrave;s - r&eacute;gl&eacute;e';
$_['text_rebate_ok']				= 'Le remboursement a &eacute;t&eacute; un succ&egrave;s';
$_['text_rebate_ok_order']			= 'Le remboursement a &eacute;t&eacute; un succ&egrave;s, le statut de la commande est pass&eacute; &agrave; rembours&eacute;e';
$_['text_void_ok']					= 'L&#8217;annulation a &eacute;t&eacute; un succ&egrave;s, le statut de la commande est pass&eacute; &agrave; annul&eacute;e';
$_['text_settle_auto']				= 'Auto';
$_['text_settle_delayed']			= 'Diff&eacute;r&eacute;';
$_['text_settle_multi']				= 'Multi';
$_['text_url_message']				= 'Vous devez fournir l&#8217;URL du magasin dans votre gestionnaire de compte Realex avant d&#8217;utiliser le mode r&eacute;&eacute;l';
$_['text_payment_info']				= 'Information de paiement';
$_['text_capture_status']			= 'Paiement captur&eacute;';
$_['text_void_status']				= 'Paiement annul&eacute;';
$_['text_rebate_status']			= 'Paiement rembours&eacute;';
$_['text_order_ref']				= 'R&eacute;f&eacute;rence commande';
$_['text_order_total']				= 'Total autoris&eacute;';
$_['text_total_captured']			= 'Total captur&eacute;';
$_['text_transactions']				= 'Transactions';
$_['text_column_amount']			= 'Montant';
$_['text_column_type']				= 'Type';
$_['text_column_date_added']		= 'Cr&eacute;&eacute;';
$_['text_confirm_void']				= '&Ecirc;tes-vous s&ucirc;re de vouloir annuler le paiement ?';
$_['text_confirm_capture']			= '&Ecirc;tes-vous s&ucirc;re de vouloir capturer le paiement ?';
$_['text_confirm_rebate']			= '&Ecirc;tes-vous s&ucirc;re de vouloir rembourser le paiement ?';
$_['text_realex']					= '<a target="_BLANK" href="http://www.realexpayments.co.uk/partner-refer?id=opencart"><img src="view/image/payment/realex.png" alt="Realex" title="Realex" style="border: 1px solid #EEEEEE;" /></a>';

// Entry
$_['entry_merchant_id']				= 'Identifiant du marchand';
$_['entry_secret']					= 'Mot secret';
$_['entry_rebate_password']			= 'Mot de passe pour le remboursement';
$_['entry_total']					= 'Total';
$_['entry_sort_order']				= 'Classement';
$_['entry_geo_zone']				= 'Zone g&eacute;ographique';
$_['entry_status']					= '&Eacute;tat';
$_['entry_debug']					= 'Journal de d&eacute;bogage';
$_['entry_live_demo']				= 'R&eacute;&eacute;l / Test';
$_['entry_auto_settle']				= 'Type de r&eacute;glement';
$_['entry_card_select']				= 'S&eacute;lectionnez une carte';
$_['entry_tss_check']				= 'Contr&ocirc;les TSS';
$_['entry_live_url']				= 'URL de production';
$_['entry_demo_url']				= 'URL de test';
$_['entry_status_success_settled']	= 'Succ&egrave;s - r&eacute;gl&eacute;';
$_['entry_status_success_unsettled'] = 'Succ&egrave;s - non r&eacute;gl&eacute;';
$_['entry_status_decline']			= 'Refus&eacute;';
$_['entry_status_decline_pending']	= 'Refus&eacute; - D&eacute;connect&eacute;';
$_['entry_status_decline_stolen']	= 'Refus&eacute; - Carte perdue ou vol&eacute;e';
$_['entry_status_decline_bank']		= 'Refus&eacute; - erreur banque';
$_['entry_status_void']				= 'Annul&eacute;e';
$_['entry_status_rebate']			= 'Rembours&eacute;e';
$_['entry_notification_url']		= 'URL de notification';

// Help
$_['help_total']					= 'Le montant total que la commande doit atteindre avant que ce mode de paiement devienne actif.';
$_['help_card_select']				= 'Demander &agrave; l&#8217;utilisateur de choisir son type de carte avant qu&#8217;il ne soit redirig&eacute;';
$_['help_notification']				= 'Vous devez fournir cette URL &agrave; Realex pour obtenir des notifications de paiement';
$_['help_debug']					= 'Autoriser le d&eacute;bogage permettra d&eacute;~crire des donn&eacute;es sensibles dans un journal. Vous devez toujours d&eacute;sactiver sauf indication contraire.';
$_['help_dcc_settle']				= 'Si votre sous-compte est activ&eacute; DCC vous devez utiliser Autosettle';

// Tab
$_['tab_account']					= 'Information API';
$_['tab_sub_account']				= 'Comptes';
$_['tab_order_status']				= '&Eacute;tat de la commande';
$_['tab_payment']					= 'Param&egrave;tres de paiement';
$_['tab_advanced']					= 'Avanc&eacute;';

// Button
$_['button_capture']				= 'Capturer';
$_['button_rebate']					= 'Rembourser';
$_['button_void']					= 'Annuler';

// Error
$_['error_merchant_id']				= 'Le num&eacute;ro d&#8217;identifiant du marchand est requis';
$_['error_secret']					= 'Le mot secret est requis';
$_['error_live_url']				= 'L&8217;URL de production est requise';
$_['error_demo_url']				= 'L&8217;URL de test est requise';
$_['error_data_missing']			= 'La date manque !';
$_['error_use_select_card']			= 'Vous devez avoir "S&eacute;lectionnez la carte" permis dans la routine de sous-compte type de carte pour travailler.';
?>
<?php
// Heading
$_['heading_title']					= 'First Data EMEA Web Service API';

// Text
$_['text_firstdata_remote']			= '<img src="view/image/payment/firstdata.png" alt="First Data" title="First Data" style="border: 1px solid #EEEEEE;" />';
$_['text_payment']					= 'Paiement';
$_['text_success']					= 'F&eacute;licitations, vous avez modifi&eacute; le compte First Data !';
$_['text_edit']                     = 'Modifier First Data EMEA Web Service API';
$_['text_card_type']				= 'Type de carte';
$_['text_enabled']					= 'Autoris&eacute;';
$_['text_merchant_id']				= 'Num&eacute;ro de boutique';
$_['text_subaccount']				= 'Sous-compte';
$_['text_user_id']					= 'um&eacute;ro d&#8217;utilisateur';
$_['text_capture_ok']				= 'L&#8217;achat a &eacute;t&eacute; un succ&egrave;s';
$_['text_capture_ok_order']			= 'L&#8217;achat a &eacute;t&eacute; un succ&egrave;s, &eacute;tat de la commande mis à jour &agrave; : succ&egrave;s - r&eacute;gl&eacute;e';
$_['text_refund_ok']				= 'Le remboursement a &eacute;t&eacute; un succ&egrave;s';
$_['text_refund_ok_order']			= 'Le remboursement a &eacute;t&eacute; un succ&egrave;s, &eacute;tat de la commande mis &agrave; jour &agrave; : rembours&eacute;e';
$_['text_void_ok']					= 'L&#8217;annulation a &eacute;t&eacute; un succ&egrave;s, &eacute;tat de la commande mis &agrave; jour &agrave; : annul&eacute;e';
$_['text_settle_auto']				= 'Vente';
$_['text_settle_delayed']			= 'Pr&eacute; autorisation';
$_['text_mastercard']				= 'Mastercard';
$_['text_visa']						= 'Visa';
$_['text_diners']					= 'Diners';
$_['text_amex']						= 'American Express';
$_['text_maestro']					= 'Maestro';
$_['text_payment_info']				= 'Information de paiement';
$_['text_capture_status']			= 'Paiement r&eacute;cup&eacute;r&eacute;';
$_['text_void_status']				= 'Paiement annul&eacute;';
$_['text_refund_status']			= 'Paiement rembours&eacute;';
$_['text_order_ref']				= 'R&eacute;f&eacute;rence commande';
$_['text_order_total']				= 'Total autoris&eacute;';
$_['text_total_captured']			= 'Total r&eacute;cup&eacute;r&eacute;';
$_['text_transactions']				= 'Transactions';
$_['text_column_amount']			= 'Montant';
$_['text_column_type']				= 'Type';
$_['text_column_date_added']		= 'Date d&#8217;ajout';
$_['text_confirm_void']				= '&Ecirc;tes-vous certain de que vouloir annuler le paiement ?';
$_['text_confirm_capture']			= '&Ecirc;tes-vous certain de que vouloir r&eacute;cup&eacute;rer le paiement ?';
$_['text_confirm_refund']			= '&Ecirc;tes-vous certain de que vouloir rembourser le paiement ?';

// Entry
$_['entry_certificate_path']		= 'Chemin du Certificat';
$_['entry_certificate_key_path']	= 'Chemin de la cl&eacute; priv&eacute;';
$_['entry_certificate_key_pw']		= 'Mot de passe de cl&eacute; priv&eacute;';
$_['entry_certificate_ca_path']		= 'Chemin CA';
$_['entry_merchant_id']				= 'Num&eacute;ro de boutique';
$_['entry_user_id']					= 'Num&eacute;ro d&#8217;utilisateur';
$_['entry_password']				= 'Mot de passe';
$_['entry_total']					= 'Total';
$_['entry_sort_order']				= 'Classement';
$_['entry_geo_zone']				= 'Zone g&eacute;ographique';
$_['entry_status']					= '&Eacute;tat';
$_['entry_debug']					= 'Enregistrement de d&eacute;bogage';
$_['entry_auto_settle']				= 'Type de r&egrave;glement';
$_['entry_status_success_settled']	= 'Succ&egrave;s - r&eacute;gl&eacute;';
$_['entry_status_success_unsettled'] = 'Succ&egrave;s - non r&eacute;gl&eacute;';
$_['entry_status_decline']			= 'Refus&eacute;';
$_['entry_status_void']				= 'Annul&eacute;';
$_['entry_status_refund']			= 'Rembours&eacute;';
$_['entry_enable_card_store']		= 'Activez les jetons de stockage de carte';
$_['entry_cards_accepted']			= 'Types de carte accept&eacute;s';

// Help
$_['help_total']					= 'Le total que la commande doit atteindre avant que cette m&eacute;thode de paiement devienne active';
$_['help_certificate']				= 'Les Certificats et la cl&eacute; priv&eacute;e ne doivent pas &ecirc;tre stoqu&eacute;s dans un r&eacute;pertoire publique de votre site';
$_['help_card_select']				= 'Demande &agrave; l&#8217;utilisateur de choisir son type de carte avant d&#8217;&ecirc;tre redirig&eacute;';
$_['help_notification']				= 'Vous devez fournir cette URL &agrave; First Data pour recevoir des notifications de paiement';
$_['help_debug']					= 'En autorisant le d&eacute;bogage, vous autorisez l&#8217;&eacute;criture des donn&eacute;es sensibles dans un fichier journal. Vous devez toujours d&eacute;sactiver sauf avis contraire';
$_['help_settle']					= 'Si vous utilisez pr&eacute; autorisation, vous devez effectuer une action post-auth dans 3-5 jours sinon votre transaction sera abandonn&eacute;e';

// Tab
$_['tab_account']					= 'Information de l&#8217;API';
$_['tab_order_status']				= '&Eacute;tat de la commande';
$_['tab_payment']					= 'Param&egrave;tres de paiement';

// Button
$_['button_capture']				= 'R&eacute;cup&eacute;rer';
$_['button_refund']					= 'Rembourser';
$_['button_void']					= 'Annuler';

// Error
$_['error_merchant_id']				= 'Le num&eacute;ro de la boutique est requis';
$_['error_user_id']					= 'Le num&eacute;ro d&#8217;utilisateur est requis';
$_['error_password']				= 'Le mot de passe est requis';
$_['error_certificate']				= 'Le chemin du Certificat est requis';
$_['error_key']						= 'La cl&eacute; du Certificat est requise';
$_['error_key_pw']					= 'Le mot de passe de cl&eacute; de Certificat est requis';
$_['error_ca']						= 'L&#8217;autorit&eacute; du Certificate (CA) est requise';
?>
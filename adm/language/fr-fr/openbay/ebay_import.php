<?php
// Heading
$_['heading_title']                     = 'Import d&#8217;article eBay';
$_['text_openbay']                      = 'OpenBay Pro';
$_['text_ebay']                         = 'eBay';

// Text
$_['text_sync_import_line1']            = '<strong>Attention !</strong> Cela importera tous vos produits eBay et construira une structure de cat&eacute;gorie dans votre magasin. Il est conseill&eacute; de supprimer toutes les cat&eacute;gories et les produits avant d&#8217;ex&eacute;cuter cette option.<br />La structure de cat&eacute;gorie des cat&eacute;gories d&#8217;eBay normales, pas les cat&eacute;gories de votre boutique (si vous avez une boutique eBay). Vous pouvez renommer, supprimer et modifier les cat&eacute;gories import&eacute;es sans affecter vos produits eBay.';
$_['text_sync_import_line3']            = 'Vous devez vous assurer que votre serveur peut accepter et traiter les grandes tailles de donn&eacute;es POST. 1000 objets eBay est d&#8217:environ 40 Mo en taille, vous aurez besoin de calculer ce que vous d&eacute;sirez. Si votre appel &eacute;choue, alors il est probable que votre r&eacute;glage est trop faible. Votre limite de m&eacute;moire de PHP doit &ecirc;tre d&#8217;environ 128Mb.';
$_['text_sync_server_size']             = 'Actuellement votre serveur peut accepter : ';
$_['text_sync_memory_size']             = 'Votre limite de m&eacute;moire PHP : ';
$_['text_import_confirm']				= 'Cela va importer tous vos objets eBay ainsi que de nouveaux produits, &ecirc;tes-vous sûr ? Cela ne peut pas &ecirc;tre annul&eacute;! Assurez-vous d&#8217;avoir d&#8217;abord une sauvegarde !';
$_['text_import_notify']				= 'Votre demande d&#8217;importation a &eacute;t&eacute; envoy&eacute; pour traitement. Une importation dure environ 1 heure par 1000 articles.';
$_['text_import_images_msg1']           = 'Les images sont en cours d&#8217;importation et de copie sur eBay. Actualisez cette page, si le nombre ne diminue pas';
$_['text_import_images_msg2']           = 'cliquer ici';
$_['text_import_images_msg3']           = 'et patientez. Plus d&#8217;informations sur les raisons de ce qui s&#8217;est pass&eacute; peut &ecirc;tre trouv&eacute; sur <a href="http://shop.openbaypro.com/index.php?route=information/faq&topic=8_45" target="_blank">here</a>';

// Entry
$_['entry_import_item_advanced']        = 'Obtenir les donn&eacute;es d&eacute;tail&eacute;es';
$_['entry_import_categories']         	= 'Importer les cat&eacute;gories';
$_['entry_import_description']			= 'Importer les descriptions des articles';
$_['entry_import']						= 'Importer les articles eBay';

// Buttons
$_['button_import']						= 'Importer';

// Help
$_['help_import_item_advanced']        	= 'Prendra 10 fois plus de temps pour importer les articles. Importe les poids, les tailles, ISBN et plus si disponible.';
$_['help_import_categories']         	= 'Construction d&#8217;une tructure de cat&eacute;gories dans votre boutique &agrave; partir des cat&eacute;gories eBay.';
$_['help_import_description']         	= 'Tout sera import&eacute;y compris HTML, compteurs etc...';

// Error
$_['error_import']                   	= '&Eacute;chec du chargemet.';
$_['error_maintenance']					= 'Votre bourique est en maintenance. L&#8217;mportation &eacute;chouera !';
$_['error_ajax_load']					= 'Impossible de se connecter au serveur';
$_['error_validation']					= 'Vous devez vous inscrire pour obtenir votre cl&eacute; API et activer le module.';
?>
<?php
// Text
$_['text_title']			= 'Facture Klarna';
$_['text_terms_fee']		= '<span id="klarna_invoice_toc"></span> (+%s)<script type="text/javascript">var terms = new Klarna.Terms.Invoice({el: \'klarna_invoice_toc\', eid: \'%s\', country: \'%s\', charge: %s});</script>';
$_['text_terms_no_fee']		= '<span id="klarna_invoice_toc"></span><script type="text/javascript">var terms = new Klarna.Terms.Invoice({el: \'klarna_invoice_toc\', eid: \'%s\', country: \'%s\'});</script>';
$_['text_additional']		= 'Klarna requiert des informations suppl&eacute;mentaires avant de pouvoir proc&eacute;der &agrave; votre commande.';
$_['text_male']				= 'Homme';
$_['text_female']			= 'Femme';
$_['text_year']				= 'Ann&eacute;e';
$_['text_month']			= 'Mois';
$_['text_day']				= 'Jour';
$_['text_comment']			= 'ID de facturation Klarna: %s\n%s/%s: %.4f';

// Entry
$_['entry_gender']			= 'Genre :';
$_['entry_pno']				= 'Date de naissance :<span class="help">(07071960)</span>';
$_['entry_dob']				= 'Date de naissance :';
$_['entry_phone_no']		= 'N&deg; de t&eacute;l&eacute;phone :<br /><span class="help">Veuillez entrer votre num&eacute;ro de t&eacute;l&eacute;phone.</span>';
$_['entry_street']			= 'Rue :<br /><span class="help">Notez que la livraison ne peut s&#8217;&eacute;ffectuer qu&#8217;&agrave; l&#8217;adresse enregistr&eacut;e lors d&#8217;un paiement avec Klarna.</span>';
$_['entry_house_no']		= 'N&deg; du domicile :<br /><span class="help">Veuillez entrer le num&eacute;ro de votre domicile.</span>';
$_['entry_house_ext']		= 'Compl&eacute;ment du domicile:<br /><span class="help">Veuillez entrer ici les extensions relatives &agrave; votre domicile. Ex. : Escalier A, B ou C, Rouge, Bleu, etc...</span>';
$_['entry_company']			= 'N&deg; d&#8217;enregistrement soci&eacute;t&eacute;:<br /><span class="help">Veuillez entrer votre num&eacute;ro d&#8217;enregistrement soci&eacute;t&eacute;</span>';

// Help
$_['help_pno']				= 'S.V.P. entrer votre num&eacute;ro de s&eacute;curit&eacute; sociale ici.';
$_['help_phone_no']			= 'S.V.P. entrer votre num&eacute;ro de t&eacute;l&eacute;phone.';
$_['help_street']			= 'S.V.P. noter que la livraison ne peut avoir lieu qu&#8217;&agrave; l&#8217;adresse enregistr&eacute;e en payant avec Klarna.';
$_['help_house_no']			= 'S.V.P. entrer votre num&eacute;ro de maison.';
$_['help_house_ext']		= 'S.V.P. soumettre votre extension de maison ici. i.e. A, B, C, Rouge, Bleu etc.';
$_['help_company']			= 'S.V.P. entrer votre num&eacute;ro SIREN ou SIRET';

// Error
$_['error_deu_terms']		= 'Vous devez accepter la politique de confidentialit&eacute; de Klarna';
$_['error_address_match']	= 'Les adresses de facturation et de livraison doivent correspondre si vous souhaitez utiliser la facturation Klarna';
$_['error_network']			= 'Une erreur est survenue lors de la connexion &agrave; Klarna. Veuillez r&eacute;essayer plus tard.';
?>
<?php
##====================================================##
## @author    : OCdevWizard                           ##
## @contact   : ocdevwizard@gmail.com                 ##
## @support   : http://help.ocdevwizard.com           ##
## @copyright : (c) OCdevWizard. Admin Security, 2018 ##
##====================================================##

// Main
$_['heading_title']                               = 'Admin Security';
$_['button_save']                                 = 'Save';
$_['button_save_and_stay']                        = 'Save and stay';
$_['button_uninstall']                            = 'Uninstall module';
$_['button_uninstall_and_remove']                 = 'Uninstall module and remove his files';
$_['button_restore']                              = 'Restore to default settings';
$_['button_cache']                                = 'Clear module cache';
$_['button_cache_backup']                         = 'Clear local backup files';
$_['button_need_help']                            = 'Help';
$_['button_need_help_email']                      = 'Send email';
$_['button_need_help_ticket']                     = 'Create ticket';
$_['button_cancel']                               = 'Back';
$_['tab_control_panel']                           = 'Control panel';
$_['tab_layout_setting']                          = 'Layout setting';
$_['tab_language_setting']                        = 'Language setting';
$_['tab_record_setting']                          = 'Record setting';
$_['tab_banned_setting']                          = 'Banned setting';
$_['tab_email_template_setting']                  = 'Email templates setting';
$_['tab_license_setting']                         = 'License';
$_['text_setting_left_menu']                      = 'Main settings';
$_['text_select_store']                           = 'Select store config';
$_['text_menu_button']                            = 'Menu';
$_['text_page_extensions']                        = 'Extension list';
$_['button_fix']                                  = 'Fix';

// Assistance
$_['text_make_a_choice']                          = '-- Make a choice --';
$_['text_none']                                   = '-- None --';
$_['button_view_more']                            = 'View more'; // for widget only
$_['text_records']                                = 'Suspicious login attempts'; // for widget only
$_['text_records_banned']                         = 'Permanently banned visitors'; // for widget only
$_['text_yes']                                    = 'Yes';
$_['text_no']                                     = 'No';
$_['text_select_all']                             = 'Select all';
$_['text_unselect_all']                           = 'Unselect all';
$_['text_processing']                             = 'Processing';
$_['text_success_processing']                     = 'Success';
$_['button_filter']                               = 'Filter';
$_['button_clear_filter']                         = 'Clear';
$_['text_enter_email']                            = 'User email';
$_['text_enter_email_template']                   = 'Email template name';
$_['entry_limit']                                 = 'Enter limit';
$_['text_no_results']                             = 'No results!';
$_['button_update']                               = 'Update';
$_['text_are_you_sure']                           = 'Are you sure?';
$_['button_close']                                = 'Close';
$_['text_open_example']                           = 'Open example';
$_['text_open_explanation']                       = 'Open explanation';
$_['button_loading']                              = 'Loading...';
$_['text_px']                                     = 'px';
$_['button_submit']                               = 'Submit';
$_['button_edit']                                 = 'Edit';
$_['button_open']                                 = 'Open';
$_['button_delete']                               = 'Delete';
$_['button_copy']                                 = 'Copy';
$_['text_not_changed']                            = 'Not changed';
$_['text_open_texteditor']                        = 'Open WYSIWYG editor';
$_['text_save_texteditor']                        = 'Save changes';
$_['column_heading']                              = 'Name';
$_['button_delete_menu']                          = 'Delete';
$_['button_delete_selected']                      = 'Delete selected';
$_['button_delete_all']                           = 'Delete all';
$_['button_copy_menu']                            = 'Copy';
$_['button_copy_selected']                        = 'Copy selected';
$_['button_copy_all']                             = 'Copy all';
$_['button_add_banned']                           = 'Add to banned list';
$_['button_remove_banned']                        = 'Disable ban';
$_['button_add_email_template']                   = 'Add email template';
$_['button_preview_result']                       = 'Preview result';
$_['button_full_width']                           = 'Toggle full width';
$_['button_limit']                                = 'Limit';
$_['column_date_added']                           = 'Date added';
$_['column_date_modified']                        = 'Date modified';
$_['column_status']                               = 'Status';
$_['text_status_enabled']                         = '<span class="label label-success text-uppercase">Enabled</span>';
$_['text_status_disabled']                        = '<span class="label label-danger text-uppercase">Disabled</span>';
$_['column_action']                               = 'Action';
$_['text_alert_error_heading']                    = 'Error!';
$_['text_alert_success_heading']                  = 'Success!';
$_['button_generate']                             = 'Generate';

// Tab - General setting
$_['tab_general_setting']                         = 'General setting';
$_['text_activate_module']                        = 'Activate module';
$_['text_access_type']                            = 'Access type';
$_['text_access_type_1']                          = 'Pattern lock';
$_['text_access_type_2']                          = 'Secret key and password';
$_['text_access_type_faq']                        = '<b>Pattern lock</b> - you can add login page protection with pattern lock, each attempt to unlock the pattern lock brings the user closer to the ban for 1 hour.<br><b>Secret key and password</b> - you can access to admin login page if you know secret key and password, else will be redirected to homepage.';
$_['text_access_attempts']                        = 'Access attempts';
$_['text_access_attempts_faq']                    = 'Enter access attempts. If the user uses all attempts then he will be blocked for 1 hour.';
$_['text_user_ip']                                = 'Exclude IP list from the verification system';
$_['text_user_ip_faq']                            = 'This function was created to simplify the entrance to the admin panel by skipping the module protection system, but nevertheless it does not cancel the authorization process. If your IP matches the current value, then you will be redirected to the login page. You can write more than one value, in this case they should be separated with a comma.';
$_['text_secret_key']                             = 'Secret key';
$_['text_secret_key_faq']                         = 'I highly recommend you to change the secret key immediately after installing the module.';
$_['text_secret_password']                        = 'Secret password';
$_['text_secret_password_faq']                    = 'I recommend you change this secret password. Secret password does not match with the password from the admin control panel.';
$_['text_link_to_backup_access']                  = 'URL to backup access';
$_['text_link_to_backup_access_faq']              = 'Use this URL if you try to go to the admin control panel from another IP.';
$_['text_pattern_size']                           = 'Pattern size';
$_['text_pattern_code']                           = 'Pattern code';
$_['text_captcha_status']                         = 'Enable Google reCAPTCHA on admin login page';
$_['text_captcha_status_faq']                     = 'You can enable additional protection to admin login page. You need to register captcha widget on <a href="https://www.google.com/recaptcha/intro/index.html" target="_blank"><u>Google reCAPTCHA page</u></a>';
$_['text_captcha_site_key']                       = 'Google reCAPTCHA site key';
$_['text_captcha_secret_key']                     = 'Google reCAPTCHA secret key';

// Tab - Basic setting
$_['tab_basic_setting']                           = 'Basic setting';
$_['text_admin_email_for_notification']           = 'Admin email';
$_['text_admin_email_for_notification_faq']       = 'You can write more than one value, in this case they should be separated with a comma.';
$_['text_admin_alert_login_attempt_status']       = 'Send notification to admin on new login attempt';
$_['text_admin_email_login_attempt_template']     = 'Custom Email HTML template for admin notifications on new login attempt';
$_['text_admin_email_login_attempt_template_faq'] = 'You can send email notification to admin on new login attempt, if you need to show individuality then you must go to <b><a style="cursor:pointer;" onclick="$(\'[href=#email-template-constructor-block]\').click();">Email template constructor</a></b> and create your own email template.';
$_['text_admin_alert_login_success_status']       = 'Send notification to admin on success user login';
$_['text_admin_email_login_success_template']     = 'Custom Email HTML template for admin notifications on success user login';
$_['text_admin_email_login_success_template_faq'] = 'You can send email notification to admin on success user login, if you need to show individuality then you must go to <b><a style="cursor:pointer;" onclick="$(\'[href=#email-template-constructor-block]\').click();">Email template constructor</a></b> and create your own email template.';
$_['text_direction_type']                         = 'Select text direction type';
$_['text_direction_type_1']                       = 'LTR (left to right)';
$_['text_direction_type_2']                       = 'RTL (right to left)';

// Tab - Layout setting
$_['text_show_on_dashboard']                      = 'Show widget on admin dashboard';
$_['text_show_on_top_notification']               = 'Show widget on admin top notification list';
$_['text_show_description']                       = 'Show additional information';
$_['text_page_background_type']                   = 'Page background type';
$_['text_page_background_type_1']                 = 'Use image';
$_['text_page_background_type_2']                 = 'Use color';
$_['text_background_color']                       = 'Background color for page';
$_['text_panel_background_color']                 = 'Background color for panel with pattern';
$_['text_panel_style_error_text_color']           = 'Color for warning message';
$_['text_background_images']                      = 'Background image for page';
$_['text_background_opacity']                     = 'Background opacity for panel with pattern';
$_['text_background_opacity_faq']                 = '0 - absolutely transparent, 1 - absolutely visible.';

// Tab - Css setting
$_['tab_css_setting']                             = 'CSS setting';
$_['text_edit_css']                               = 'Edit main stylesheet';
$_['text_edit_css_rtl']                           = 'Edit stylesheet for RTL';
$_['button_save_css']                             = 'Save CSS';
$_['button_restore_css']                          = 'Restore default CSS';

// Tab - Import/Export config setting
$_['tab_config_import_export_setting']            = 'Import/Export config setting';
$_['button_export']                               = 'Export';
$_['button_import']                               = 'Import';
$_['text_restore_from_external_file']             = 'Restore from external file';
$_['text_restore_from_local_file']                = 'Restore from local file';
$_['text_export']                                 = 'Export module settings';
$_['text_select_file']                            = 'Select file';

// Tab - Language basic setting
$_['tab_basic_language_setting']                  = 'Basic language setting';
$_['entry_name']                                  = 'Name';
$_['default_name']                                = 'Restricted area!';
$_['entry_meta_title']                            = 'Meta tag title';
$_['default_meta_title']                          = 'Restricted area!';
$_['entry_pattern']                               = 'Warning message for pattern protection';
$_['default_pattern']                             = 'Warning! Pattern is not correct!<br/>You have {count_attempt_left} tryses before 1 hour ban!';
$_['entry_pattern_faq']                           = '<b>{count_attempt_left}</b> - use this short code to display how many attempts have user before ban.';
$_['entry_pattern_attempt']                       = 'Warning message for access attempts';
$_['default_pattern_attempt']                     = 'Warning! Your account has exceeded allowed number of login attempts.<br/>Please try again in 1 hour.';
$_['entry_description']                           = 'Additional information';
$_['default_description']                         = 'Your text may be here.';

// Tab - Record constructor setting
$_['tab_record_constructor_setting']              = 'Record list';
$_['text_info_record']                            = 'Record information';

// Tab - Import/Export record setting
$_['tab_record_import_export_setting']            = 'Import/Export records';

// Tab - Banned constructor setting
$_['tab_banned_constructor_setting']              = 'Banned list';
$_['text_add_banned']                             = 'Add banned';
$_['text_edit_banned']                            = 'Edit banned';
$_['entry_ip']                                    = 'IP';
$_['placeholder_ip']                              = 'Entry ip';
$_['column_ip']                                   = 'IP';

// Tab - Import/Export banned list setting
$_['tab_banned_import_export_setting']            = 'Import/Export banned list';

// Tab - Email template constructor setting
$_['tab_email_template_constructor_setting']      = 'Email template constructor';

// Tab - Modal email template general setting
$_['text_edit_email_template']                    = 'Edit email template';
$_['text_add_email_template']                     = 'Add email template';
$_['text_assignment_email_template']              = 'Assignment';
$_['text_assignment_email_template_1']            = 'For admin notifications on new login attempt';
$_['text_assignment_email_template_2']            = 'For admin notifications on new success login';
$_['text_assignment_email_template_faq']          = '<b>For admin notifications on new login attempt</b> - select it for email notification to admin when somebody trying to login in admin area.<br><b>For admin notifications on new success login</b> - select it for email notification to admin when somebody successfully logged in admin area.';
$_['default_email_template_name']                 = 'Custom template';
$_['text_email_template_subject']                 = 'Enter subject';
$_['default_email_template_subject']              = '{store_name}';
$_['text_email_template_html']                    = 'Email template';
$_['text_assignment_email_template_1_subject']    = '<b>Visitor short-codes</b><br>{ip} - user IP<br><b>Record short-codes</b><br>{record_id} - record ID value<br>{date_added} - record date added value<br><b>Store short-codes</b><br>{store_name} - store name';
$_['text_assignment_email_template_1_template']   = '<b>Visitor short-codes</b><br>{ip} - user IP<br><b>Record short-codes</b><br>{record_id} - record ID value<br>{date_added} - record date added value<br>{permanent_user_ban_url} - give permanent ban for user<br><b>Store short-codes</b><br>{store_name} - store name';
$_['text_assignment_email_template_2_subject']    = '<b>Visitor short-codes</b><br>{ip} - user IP<br>{username} - user username<br><b>Record short-codes</b><br>{record_id} - record ID value<br>{date_added} - record date added value<br><b>Store short-codes</b><br>{store_name} - store name';
$_['text_assignment_email_template_2_template']   = '<b>Visitor short-codes</b><br>{username} - user username<br>{ip} - user IP<br><b>Record short-codes</b><br>{record_id} - record ID value<br>{date_added} - record date added value<br>{disable_user_url} - disable user account<br><b>Store short-codes</b><br>{store_name} - store name';
$_['entry_status']                                = 'Status';
$_['entry_system_name']                           = 'System name';
$_['text_edit_template']                          = 'edit template';
$_['text_no_email_templates']                     = 'There are no available templates. You need to <b><a style="cursor:pointer;" onclick="open_email_template(0);">create one Email template</a></b> first.';

// Tab - Import/Export email template setting
$_['tab_email_template_import_export_setting']    = 'Import/Export email template setting';

// Tab - License information
$_['tab_license_extension_setting']               = 'License information';
$_['text_license_key']                            = 'License key';
$_['button_apply_license_code']                   = 'Applay license key';
$_['text_license_text']                           = 'License type';
$_['text_license_holder']                         = 'License holder';
$_['text_license_expires']                        = 'License expires on';
$_['text_license_date_end']                       = '%s (%s remaining)';
$_['text_license_expire_day_1']                   = 'day';
$_['text_license_expire_day_2']                   = 'days';
$_['text_license_expire_forever']                 = 'No time limit';
$_['text_license_end']                            = 'Expired';
$_['button_renew_license']                        = 'Renew a license';
$_['text_request_license_code']                   = 'Request license code';
$_['text_license_expire_ended']                   = 'You are running an unlicensed version of this extension! You can not get free technical support and using fresh updates until you activate this license!';
$_['text_request_license_code_left_side']         = '<p>With the license code you can activate module and use him on your site according to module license policy*.</p><p>If you do not have license code, please press the button below to get him.</p><p>Please, no need to repeat request too fast if you have send one previuselly. Just wait for response from support staff, useally it takes couple of minutes, but in some cases it takes more time. After 24 hours please send request once again.</p><p><button type="button" onclick="open_license_code_request();" class="btn btn-warning btn-sm"><i class="fa fa-envelope-o"></i> Request license code</button></p><hr><div style="font-size: 11px;">* - you can find Licensing Policy file in the module ZIP archive.</div>';
$_['text_request_license_code_right_side_1']      = '<p>Here is a limited list of official sites where you can buy a module. Buying a module here you will automatically receive technical free* support from the module developer for one year.</p>';
$_['text_request_license_code_right_side_2']      = '<div style="font-size: 11px;">* - technical support is provided free of charge. Please note that the paid technical support is performed in cases where there is a conflict with an outside modules/products/templates.</div>';

// Success
$_['text_success']                                = 'Settings of module '.$_['heading_title'].' is updated!';
$_['text_success_install']                        = 'The module '.$_['heading_title'].' is successfully installed!';
$_['text_success_uninstall']                      = 'The module '.$_['heading_title'].' is successfully uninstalled!';
$_['text_success_config_restored']                = 'Settings of module '.$_['heading_title'].' is restored!';
$_['text_success_record_restored']                = 'Records of module '.$_['heading_title'].' is restored!';
$_['text_success_banned_restored']                = 'Banned user list of module '.$_['heading_title'].' is restored!';
$_['text_success_email_template_restored']        = 'Email templates of module '.$_['heading_title'].' is restored!';
$_['text_success_cache']                          = 'Module cache is successfully removed!';
$_['text_success_cache_backup']                   = 'Backup files from local storage is successfully removed!';
$_['text_success_task']                           = 'Task completed successfully!';
$_['text_success_banned_add']                     = 'Banned rule is successfully created!';
$_['text_success_banned_edit']                    = 'Banned rule is successfully updated!';
$_['text_success_email_template_add']             = 'Email template is successfully created!';
$_['text_success_email_template_edit']            = 'Email template is successfully updated!';
$_['text_success_generate_password']              = 'Password is successfully generated!';
$_['text_success_css_saved']                      = 'CSS successfully saved!';
$_['text_success_css_restored']                   = 'CSS successfully restored!';

// Error
$_['error_warning']                               = 'Module settings will not be saved until you fix the errors. Please check the form carefully for errors!';
$_['error_permission']                            = 'You are not authorized to change the module '.$_['heading_title'].'!';
$_['error_for_all_field']                         = 'This field must not be empty!';
$_['error_for_all_field_1']                       = 'This field must be less greater than %s symbols!';
$_['error_for_all_field_2']                       = 'This field must be between %s and %s!';
$_['error_for_all_field_2_1_6']                   = 'This field must be between 1 and 6!';
$_['error_for_all_field_2_1_255']                 = 'This field must be between 1 and 255!';
$_['error_for_all_field_2_1_5000']                = 'This field must be between 1 and 5000!';
$_['error_for_all_field_1_255']                   = 'This field must be less greater than 255 symbols!';
$_['error_for_all_field_1_5000']                  = 'This field must be less greater than 5000 symbols!';
$_['error_not_isset_email_template']              = 'You need to add at least one email template!';
$_['error_compatible_version']                    = 'You have installed a incompatible version of module for this opencart shop!';
$_['error_task']                                  = 'Task fails!';
$_['error_failed_load_stylesheet']                = 'Stylesheet file not loaded!';
$_['error_license_key']                           = 'Enter the license key!';
$_['error_license_key_not_valid']                 = 'License key is not valid!';
$_['error_empty_email_template']                  = 'You need to <a style="cursor:pointer;" onclick="$(\'[href=#email-template-constructor-block]\').click();">create</a>/<a style="cursor:pointer;" onclick="$(\'[href=#basic-block]\').click();">select</a> Custom HTML template!';
$_['error_template_preview']                      = 'Template is not found!';
$_['error_license_server']                        = 'The license server performs temporary technical work. The function of saving the module settings is temporarily disabled, to ensure the integrity of this data. Try to resume work with this page later. Thank you for your understanding and patience!';
$_['error_technical_url']                         = 'Technical url will be available after saving the settings!';
$_['error_connection_waiting_limit_exceeded']     = 'Connection waiting limit exceeded!';
$_['error_recaptcha']                             = 'Captcha is not valid!';
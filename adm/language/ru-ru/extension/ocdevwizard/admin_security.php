<?php
##====================================================##
## @author    : OCdevWizard                           ##
## @contact   : ocdevwizard@gmail.com                 ##
## @support   : http://help.ocdevwizard.com           ##
## @copyright : (c) OCdevWizard. Admin Security, 2018 ##
##====================================================##

// Main
$_['heading_title']                               = 'Дополнительная защита админки';
$_['button_save']                                 = 'Сохранить';
$_['button_save_and_stay']                        = 'Сохранить и остаться';
$_['button_uninstall']                            = 'Удалить модуль';
$_['button_uninstall_and_remove']                 = 'Удалить модуль вместе с его файлами';
$_['button_restore']                              = 'Восстановить настройки по умолчанию';
$_['button_cache']                                = 'Удалить кеш модуля';
$_['button_cache_backup']                         = 'Удалить файлы восстановления';
$_['button_need_help']                            = 'Помощь';
$_['button_need_help_email']                      = 'Через email сообщение';
$_['button_need_help_ticket']                     = 'Через сайт поддержки';
$_['button_cancel']                               = 'Назад';
$_['tab_control_panel']                           = 'Панель управления';
$_['tab_layout_setting']                          = 'Настройки отображения';
$_['tab_language_setting']                        = 'Настройки перевода';
$_['tab_record_setting']                          = 'Список записей';
$_['tab_banned_setting']                          = 'Заблокированные';
$_['tab_email_template_setting']                  = 'Настройки E-mail шаблонов';
$_['tab_license_setting']                         = 'Лицензия';
$_['text_setting_left_menu']                      = 'Главные настройки';
$_['text_select_store']                           = 'Выберите магазин';
$_['text_menu_button']                            = 'Меню';
$_['text_page_extensions']                        = 'Список расширений';
$_['button_fix']                                  = 'Исправить';

// Assistance
$_['text_make_a_choice']                          = '-- Выберите --';
$_['text_none']                                   = '-- Нет --';
$_['button_view_more']                            = 'Подробнее'; // for widget only
$_['text_records']                                = 'Подозрительные попытки входа'; // for widget only
$_['text_records_banned']                         = 'Постоянно забаненные посетители'; // for widget only
$_['text_yes']                                    = 'Да';
$_['text_no']                                     = 'Нет';
$_['text_select_all']                             = 'Выбрать все';
$_['text_unselect_all']                           = 'Отменить все';
$_['text_processing']                             = 'Выполнение';
$_['text_success_processing']                     = 'Успех';
$_['button_filter']                               = 'Фильтр';
$_['button_clear_filter']                         = 'Очистить';
$_['text_enter_email']                            = 'E-mail';
$_['text_enter_email_template']                   = 'Название e-mail шаблона';
$_['entry_limit']                                 = 'Лимит';
$_['text_no_results']                             = 'Нет результатов!';
$_['button_update']                               = 'Обновить';
$_['text_are_you_sure']                           = 'Вы уверены?';
$_['button_close']                                = 'Закрыть';
$_['text_open_example']                           = 'Открыть пример';
$_['text_open_explanation']                       = 'Открыть объяснение';
$_['button_loading']                              = 'Загрузка...';
$_['text_px']                                     = 'px';
$_['button_submit']                               = 'Сохранить';
$_['button_edit']                                 = 'Редактировать';
$_['button_open']                                 = 'Открыть';
$_['button_delete']                               = 'Удалить';
$_['button_copy']                                 = 'Копировать';
$_['text_not_changed']                            = 'Без изменений';
$_['text_open_texteditor']                        = 'Открыть WYSIWYG редактор';
$_['text_save_texteditor']                        = 'Сохранить изменения';
$_['column_heading']                              = 'Название';
$_['button_delete_menu']                          = 'Удалить';
$_['button_delete_selected']                      = 'Удалить выбранные';
$_['button_delete_all']                           = 'Удалить все';
$_['button_copy_menu']                            = 'Копировать';
$_['button_copy_selected']                        = 'Копировать выбранные';
$_['button_copy_all']                             = 'Копировать все';
$_['button_add_banned']                           = 'Добавить блокировку';
$_['button_remove_banned']                        = 'Убрать блокировку';
$_['button_add_email_template']                   = 'Добавить E-mail шаблон';
$_['button_preview_result']                       = 'Предпросмотр результата';
$_['button_full_width']                           = 'На всю ширину';
$_['button_limit']                                = 'Лимит';
$_['column_date_added']                           = 'Добавлен';
$_['column_date_modified']                        = 'Изменен';
$_['column_status']                               = 'Статус';
$_['text_status_enabled']                         = '<span class="label label-success text-uppercase">Включен</span>';
$_['text_status_disabled']                        = '<span class="label label-danger text-uppercase">Отключен</span>';
$_['column_action']                               = 'Действие';
$_['text_alert_error_heading']                    = 'Ошибка!';
$_['text_alert_success_heading']                  = 'Успех!';
$_['button_generate']                             = 'Генерировать';

// Tab - General setting
$_['tab_general_setting']                         = 'Главные настройки';
$_['text_activate_module']                        = 'Активировать модуль';
$_['text_access_type']                            = 'Тип проверки доступа';
$_['text_access_type_1']                          = 'Графический ключ';
$_['text_access_type_2']                          = 'Секретный ключ и пароль';
$_['text_access_type_faq']                        = '<b>Графический ключ</b> - вы можете добавить защиту страницы входа с помощью графического ключа, каждая неправильная попытка входа приближает пользователя к бану на 1 час.<br><b>Секретный ключ и пароль</b> - вы можете получить доступ к странице входа администратора, если знаете секретный ключ и пароль, иначе будете перенаправлены на домашнюю страницу.';
$_['text_access_attempts']                        = 'Кол-во безуспешных попыток доступа';
$_['text_access_attempts_faq']                    = 'Введите кол-во безуспешных попыток доступа. Если пользователь использует все попытки, он будет заблокирован на 1 час.';
$_['text_user_ip']                                = 'Исключить список IP из системы проверки';
$_['text_user_ip_faq']                            = 'Эта функция была создана для упрощения входа в админ-панель путем пропуска системы защиты модуля, но тем не менее не отменяет стандартную страницу авторизации. Если ваш IP совпадает с текущим значением, вы будете перенаправлены сразу на страницу входа. Вы можете написать более одного значения, в этом случае они должны быть разделены запятой.';
$_['text_secret_key']                             = 'Секретный ключ';
$_['text_secret_key_faq']                         = 'Я настоятельно рекомендую вам сменить секретный ключ сразу после установки модуля.';
$_['text_secret_password']                        = 'Секретный пароль';
$_['text_secret_password_faq']                    = 'Я рекомендую вам изменить этот секретный пароль. Секретный пароль не должен совпадать с паролем из панели управления администратора.';
$_['text_link_to_backup_access']                  = 'URL для резервного входа';
$_['text_link_to_backup_access_faq']              = 'Используйте этот URL, если вы пытаетесь перейти к панели управления администратора с другого IP.';
$_['text_pattern_size']                           = 'Размер графического ключа';
$_['text_pattern_code']                           = 'Нарисуйте графический ключ';
$_['text_captcha_status']                         = 'Включить Google reCAPTCHA для страницы авторизации в админке';
$_['text_captcha_status_faq']                     = 'Вы можете включить Google reCAPTCHA для страницы авторизации в админке. Вам необходимо зарегистрировать виджет на <a href="https://www.google.com/recaptcha/intro/index.html" target="_blank"><u>Google reCAPTCHA странице</u></a>';
$_['text_captcha_site_key']                       = 'Google reCAPTCHA site key';
$_['text_captcha_secret_key']                     = 'Google reCAPTCHA secret key';

// Tab - Basic setting
$_['tab_basic_setting']                           = 'Базовые настройки';
$_['text_admin_email_for_notification']           = 'Email администратора';
$_['text_admin_email_for_notification_faq']       = 'Вы можете написать более одного значения, в этом случае они должны быть разделены запятой.';
$_['text_admin_alert_login_attempt_status']       = 'Отправить уведомление администратору при новой попытке входа';
$_['text_admin_email_login_attempt_template']     = 'E-mail HTML-шаблон для уведомлений администратора при новой попытке входа';
$_['text_admin_email_login_attempt_template_faq'] = 'Вы можете отправить уведомление по электронной почте администратору при новой попытке входа. Если вам нужно показать индивидуальность, тогда вы должны перейти на <b><a style="cursor:pointer;" onclick="$(\'[href=#email-template-constructor-block]\').click();">Конструктор html шаблонов</a></b> и создать свой шаблон.';
$_['text_admin_alert_login_success_status']       = 'Отправить уведомление администратору при успешном входе пользователя';
$_['text_admin_email_login_success_template']     = 'E-mail HTML-шаблон для уведомлений администратора при успешном входе пользователя';
$_['text_admin_email_login_success_template_faq'] = 'Вы можете отправить уведомление по электронной почте администратору при успешном входе пользователя. Если вам нужно показать индивидуальность, тогда вы должны перейти на <b><a style="cursor:pointer;" onclick="$(\'[href=#email-template-constructor-block]\').click();">Конструктор html шаблонов</a></b> и создать свой шаблон.';
$_['text_direction_type']                         = 'Выберите ориентацию текста в модуле';
$_['text_direction_type_1']                       = 'LTR (с лева на право)';
$_['text_direction_type_2']                       = 'RTL (с права на лево)';

// Tab - Layout setting
$_['text_show_on_dashboard']                      = 'Показать виджет на панели управления администратора';
$_['text_show_on_top_notification']               = 'Показать виджет в верхнем списке уведомлений администратора';
$_['text_show_description']                       = 'Показать описание';
$_['text_page_background_type']                   = 'Тип заднего фона страницы';
$_['text_page_background_type_1']                 = 'Отображать картинку';
$_['text_page_background_type_2']                 = 'Отображать цвет';
$_['text_background_color']                       = 'Цвет фона страницы';
$_['text_panel_background_color']                 = 'Цвет фона панели графического ключа';
$_['text_panel_style_error_text_color']           = 'Цвет текста для предупреждающего сообщения';
$_['text_background_images']                      = 'Картинка фона страницы';
$_['text_background_opacity']                     = 'Прозрачность фона панели графического ключа';
$_['text_background_opacity_faq']                 = '0 - абсолютно прозрачный, 1 - абсолютно видимый.';

// Tab - Css setting
$_['tab_css_setting']                             = 'Настройки CSS';
$_['text_edit_css']                               = 'Редактировать гланый файл стилей модуля';
$_['text_edit_css_rtl']                           = 'Редактировать файл стилей RTL';
$_['button_save_css']                             = 'Сохранить CSS';
$_['button_restore_css']                          = 'Восстановить CSS по умолчанию';

// Tab - Import/Export config setting
$_['tab_config_import_export_setting']            = 'Импорт/Экспорт главных настроек';
$_['button_export']                               = 'Экспорт';
$_['button_import']                               = 'Импорт';
$_['text_restore_from_external_file']             = 'Восстановить из внешнего файла';
$_['text_restore_from_local_file']                = 'Восстановить из локального хранилища';
$_['text_export']                                 = 'Экспортировать настройки модуля';
$_['text_select_file']                            = 'Выберите файл';

// Tab - Language basic setting
$_['tab_basic_language_setting']                  = 'Базовые настройки перевода';
$_['entry_name']                                  = 'Название';
$_['default_name']                                = 'Доступ ограничен!';
$_['entry_meta_title']                            = 'Meta tag title';
$_['default_meta_title']                          = 'Доступ ограничен!';
$_['entry_pattern']                               = 'Сообщение ошибки для графического ключа';
$_['default_pattern']                             = 'Предупреждение! Графический ключ неправильный!<br>У вас есть {count_attempt_left} попыток до блокировки на 1 час!';
$_['entry_pattern_faq']                           = '<b>{count_attempt_left}</b> - используйте эту код-маску, чтобы отобразить сколько попыток осталось у пользователя до бана.';
$_['entry_pattern_attempt']                       = 'Предупреждение об исчерпании попыток доступа';
$_['default_pattern_attempt']                     = 'Предупреждение! Вы превысили допустимое количество попыток входа в систему.<br>Повторите попытку через 1 час.';
$_['entry_description']                           = 'Описание';
$_['default_description']                         = 'Здесь может быть ваш текст.';

// Tab - Record constructor setting
$_['tab_record_constructor_setting']              = 'Список записей';
$_['text_info_record']                            = 'Информация по записи';

// Tab - Import/Export record setting
$_['tab_record_import_export_setting']            = 'Импорт/Экспорт записей';

// Tab - Banned constructor setting
$_['tab_banned_constructor_setting']              = 'Список блокировок';
$_['text_add_banned']                             = 'Добавить блокировку';
$_['text_edit_banned']                            = 'Редактировать блокировку';
$_['entry_ip']                                    = 'IP';
$_['placeholder_ip']                              = 'Введите ip';
$_['column_ip']                                   = 'IP';

// Tab - Import/Export banned list setting
$_['tab_banned_import_export_setting']            = 'Импорт/Экспорт списка блокировок';

// Tab - Email template constructor setting
$_['tab_email_template_constructor_setting']      = 'Конструктор email шаблонов';

// Tab - Modal email template general setting
$_['text_edit_email_template']                    = 'Редактировать email шаблон';
$_['text_add_email_template']                     = 'Добавить email шаблон';
$_['text_assignment_email_template']              = 'Назначение';
$_['text_assignment_email_template_1']            = 'Для уведомлений администратора при новой попытке входа';
$_['text_assignment_email_template_2']            = 'Для уведомлений администратора о новом успешном входе в систему';
$_['text_assignment_email_template_faq']          = '<b>Для уведомлений администратора при новой попытке входа</b> - выберите его для уведомления по электронной почте для администратора, когда кто-то пытается войти в админку.<br><b>Для уведомлений администратора о новом успешном входе в систему</b> - выберите его для уведомления по электронной почте для администратора, когда кто-то успешно вошел в админку.';
$_['default_email_template_name']                 = 'Кастомизированный шаблон';
$_['text_email_template_subject']                 = 'Введите заголовок';
$_['default_email_template_subject']              = '{store_name}';
$_['text_email_template_html']                    = 'Html шаблон';
$_['text_assignment_email_template_1_subject']    = '<b>Код-маска пользователя</b><br>{ip} - IP пользователя<br><b>Код-маска записи</b><br>{record_id} - id записи<br>{date_added} - дата создания записи<br><b>Код-маска магазина</b><br>{store_name} - название магазина';
$_['text_assignment_email_template_1_template']   = '<b>Код-маска пользователя</b><br>{ip} - IP пользователя<br><b>Код-маска записи</b><br>{record_id} - id записи<br>{date_added} - дата создания записи<br>{permanent_user_ban_url} - дать постоянный бан для пользователя<br><b>Код-маска магазина</b><br>{store_name} - название магазина';
$_['text_assignment_email_template_2_subject']    = '<b>Код-маска пользователя</b><br>{ip} - IP пользователя<br>{username} - user username<br><b>Код-маска записи</b><br>{record_id} - id записи<br>{date_added} - дата создания записи<br><b>Код-маска магазина</b><br>{store_name} - название магазина';
$_['text_assignment_email_template_2_template']   = '<b>Код-маска пользователя</b><br>{username} - user username<br>{ip} - IP пользователя<br><b>Код-маска записи</b><br>{record_id} - id записи<br>{date_added} - дата создания записи<br>{disable_user_url} - отключить учетную запись пользователя<br><b>Код-маска магазина</b><br>{store_name} - название магазина';
$_['entry_status']                                = 'Статус';
$_['entry_system_name']                           = 'Системное название';
$_['text_edit_template']                          = 'редактировать';
$_['text_no_email_templates']                     = 'Доступных шаблонов не обнаружено. Вам нужно <b><a style="cursor:pointer;" onclick="open_email_template(0);">создать хотя бы один Email шаблон</a></b>.';

// Tab - Import/Export email template setting
$_['tab_email_template_import_export_setting']    = 'Импорт/Экспорт Email шаблонов';

// Tab - License information
$_['tab_license_extension_setting']               = 'Информация о лицензии';
$_['text_license_key']                            = 'Ключ активации';
$_['button_apply_license_code']                   = 'Применить ключ активации';
$_['text_license_text']                           = 'Тип лицензии';
$_['text_license_holder']                         = 'Держатель лицензии';
$_['text_license_expires']                        = 'Заканчивается через';
$_['text_license_date_end']                       = '%s (%s осталось)';
$_['text_license_expire_day_1']                   = 'день';
$_['text_license_expire_day_2']                   = 'дней';
$_['text_license_expire_forever']                 = 'Неограниченно';
$_['text_license_end']                            = 'Закончилась';
$_['button_renew_license']                        = 'Перевыпустить';
$_['text_request_license_code']                   = 'Запросить код активации';
$_['text_license_expire_ended']                   = 'Вы используете нелицензированную версию этого модуля! Вы не можете получить бесплатную техническую поддержку и использовать свежие обновления, пока не активируете эту лицензию!';
$_['text_request_license_code_left_side']         = '<p>С кодом активации вы можете активировать модуль и использовать его на своем сайте в соответствии с Лицензионным соглашением* на модуль.</p><p>Если у вас нет кода активации, нажмите кнопку ниже, чтобы получить его.</p><p>Пожалуйста, не нужно повторять запрос слишком часто, если вы отправили один из них ранее. Дождитесь ответа от службы поддержки, обычно это занимает пару минут, но в некоторых случаях это может занять больше времени. Через 24 часа отправьте запрос еще раз.</p><p><button type="button" onclick="open_license_code_request();" class="btn btn-warning btn-sm"><i class="fa fa-envelope-o"></i> Запросить ключ активации</button></p><hr><div style="font-size: 11px;">* - вы можете найти файл Лицензионного соглашения в ZIP-архиве модуля.</div>';
$_['text_request_license_code_right_side_1']      = '<p>Вот список официальных сайтов, на которых вы можете купить модуль. Приобретая модуль здесь, вы автоматически получите бесплатную техническую поддержку от разработчика модуля в течение одного года.</p>';
$_['text_request_license_code_right_side_2']      = '<div style="font-size: 11px;">* - техническая поддержка предоставляется бесплатно. Обратите внимание, что платная техническая поддержка выполняется в случаях, когда существует конфликт с сторонними модулями/продуктами/шаблонами.</div>';

// Success
$_['text_success']                                = 'Настройки модуля '.$_['heading_title'].' изменены!';
$_['text_success_install']                        = 'Модуль '.$_['heading_title'].' успешно установлен!';
$_['text_success_uninstall']                      = 'Модуль '.$_['heading_title'].' успешно удален!';
$_['text_success_config_restored']                = 'Настройки модуля '.$_['heading_title'].' восстановлены!';
$_['text_success_record_restored']                = 'Список записей модуля '.$_['heading_title'].' восстановлен!';
$_['text_success_banned_restored']                = 'Список блокировок модуля '.$_['heading_title'].' восстановлен!';
$_['text_success_email_template_restored']        = 'Email шаблоны модуля '.$_['heading_title'].' восстановлены!';
$_['text_success_cache']                          = 'Кеш модуля успешно очищен!';
$_['text_success_cache_backup']                   = 'Файлы восстановления успешно удалены!';
$_['text_success_task']                           = 'Задание выполненно успешно!';
$_['text_success_banned_add']                     = 'Блокировка успешно создана!';
$_['text_success_banned_edit']                    = 'Блокировка успешно изменена!';
$_['text_success_email_template_add']             = 'Email шаблон успешно создан!';
$_['text_success_email_template_edit']            = 'Email шаблон успешно изменен!';
$_['text_success_generate_password']              = 'Пароль успешно сгенерирован!';
$_['text_success_css_saved']                      = 'Файл CSS успешно сохранен!';
$_['text_success_css_restored']                   = 'Файл CSS успешно восстановлен!';

// Error
$_['error_warning']                               = 'Настройки модуля не будут сохранены до тех пор, пока вы не исправите ошибки. Пожалуйста, внимательно проверьте форму на наличие ошибок!';
$_['error_permission']                            = 'У вас нет доступа изменять модуль '.$_['heading_title'].'!';
$_['error_for_all_field']                         = 'Это поле не должно быть пустым!';
$_['error_for_all_field_1']                       = 'Это поле должно быть меньше чем %s символов!';
$_['error_for_all_field_2']                       = 'Это поле должно быть между %s и %s символами!';
$_['error_for_all_field_2_1_6']                   = 'Это поле должно быть между 1 и 6 символами!';
$_['error_for_all_field_2_1_255']                 = 'Это поле должно быть между 1 и 255 символами!';
$_['error_for_all_field_2_1_5000']                = 'Это поле должно быть между 1 и 5000 символами!';
$_['error_for_all_field_1_255']                   = 'Это поле должно быть меньше чем 255 символов!';
$_['error_for_all_field_1_5000']                  = 'Это поле должно быть меньше чем 5000 символов!';
$_['error_not_isset_email_template']              = 'Вам нужно добавить хотя бы один Email шаблон!';
$_['error_compatible_version']                    = 'Вы установили несовместимую версию модуля с вашим магазином!';
$_['error_task']                                  = 'Не удалось выполнить задачу!';
$_['error_failed_load_stylesheet']                = 'Файл стилей не удалось загрузить!';
$_['error_license_key']                           = 'Введите ключ активации!';
$_['error_license_key_not_valid']                 = 'Лицензионный ключ недействителен!';
$_['error_empty_email_template']                  = 'Нужно <a style="cursor:pointer;" onclick="$(\'[href=#email-template-constructor-block]\').click();">создать</a>/<a style="cursor:pointer;" onclick="$(\'[href=#basic-block]\').click();">выбрать</a> Html шаблон!';
$_['error_template_preview']                      = 'Html шаблон не найден!';
$_['error_license_server']                        = 'На сервере лицензирования выполняются временные технические работы. Функция сохранения настроек модуля временно отключена, для обеспечения целосности этих данных. Попробуйте возобновить работу с этой страницой немного позже. Спасибо за понимание и терпение.';
$_['error_technical_url']                         = 'Технический URL будет доступен после сохранения настроек!';
$_['error_connection_waiting_limit_exceeded']     = 'Превышен лимит ожидания соединения!';
$_['error_recaptcha']                             = 'Капча недействительна!';
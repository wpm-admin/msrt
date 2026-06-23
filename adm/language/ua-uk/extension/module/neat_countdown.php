<?php

// The strings to be shown in the extension's list.
$_['heading_title'] = 'Countdown';
$_['text_success'] = 'Успіх: Модуль Neat Countdown змінено!';

/* I despise Opencart's method of providing views with language strings, so
   I extended it to be a less verbose and more reliable one.
   $_['domain']['key'] in here should become $domain_key in the view.*/

$_['common'] = array(
    'edit' => 'Налаштування модуля',
    'home' => 'Головна',
    'extensions' => 'Модулі',
    'modules' => 'Модулі',
    'save' => 'Зберегти',
    'cancel' => 'Відмінити',
    'error' => 'ПОМИЛКА: ',
    'timezone_warning' => 'ПОПЕРЕДЖЕННЯ: Для правильної роботи акцій потрібно мати налаштовані часові пояси у PHP і на базі даних. Це налаштування не є призначенням цього модуля.',
    'timezone_warning_close' => 'Закрити',
    'timezone_warning_close_never' => 'Закрити і знову не показувати',
);

$_['form'] = array(
    'name' => 'Назва модуля',
    'status' => 'Стан:',
    'enabled' => 'Увімкнуто',
    'disabled' => 'Вимкнено',
    'view' => 'Вид:',
    'textual' => 'Текстовий',
    'simple' => 'Простий',
    'layouts' => 'Показувати в макетах:',
    'layouts_help' => 'запропоновані макети виділені <b>жирним</b>',
    'featured_label' => 'Показувати в модулі «Популярні»',
    'clock_label' => 'Годинник:',
    'clock_browser_label' => 'Браузер',
    'clock_browser_help' => 'Використовувати годинник операційної системи користувача.',
    'clock_server_label' => 'Сервер',
    'clock_server_help' => 'Передати серверний час у браузер. Це може бути корисним, якщо користувач має неправильні налаштування часу у своїй операційній системі, але точність цього методу мала і користувач може заплутатись.',
    'clock_combo_label' => 'Комбо',
    'clock_combo_help' => 'Використовувати серверний час тільки якщо різниця із браузерним часом дуже велика.',
    'clock_critdiff' => 'Критична різниця (с):',
    'adv_setting' => 'розширене налаштування',
);

$_['error'] = array(
    'permission' => 'Ви не маєте прав редагувати модуль Neat Countdown.',
    'name' => 'Ім&apos;я обов&apos;язкове і повинне складатися з 1&#8211;200 дозволених символів.',
    'status' => 'Стан обов&apos;язковий і має бути 0 або 1.',
    'view' => 'Вид відліку обов&aposязковий і має бути правильним.',
    'layouts' => 'Макети обов&apos;язкові і мають бути правильними.',
    'clock' => 'Годинник обов&aposязковий і має бути правильним.',
    'clock--critdiff' => 'Критична різниця має бути позитивним і не величезним цілим числом.',
);

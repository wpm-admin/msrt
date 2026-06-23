<?php

// The strings to be shown in the extension's list.
$_['heading_title'] = 'Countdown';
$_['text_success'] = 'Настройки модуля Neat Countdown успешно изменены.';

/* I despise Opencart's method of providing views with language strings, so
   I extended it to be a less verbose and more reliable one.
   $_['domain']['key'] in here should become $domain_key in the view.*/

$_['common'] = array(
    'edit' => 'Настройки модуля',
    'home' => '<i class="fa fa-home fa-lg"></i>',
    'extensions' => 'Расширения',
    'modules' => 'Модули',
    'save' => 'Сохранить',
    'cancel' => 'Отменить',
    'error' => 'ОШИБКА: ',
    'timezone_warning' => 'ПРЕДУПРЕЖДЕНИЕ: Для правильной работы акций нужно иметь настроенные часовые пояса в PHP и на базе данних. Эти настройки не есть назначением этого модуля.',
    'timezone_warning_close' => 'Закрыть',
    'timezone_warning_close_never' => 'Закрыть и снова не показывать',
);

$_['form'] = array(
    'name' => 'Имя модуля',
    'status' => 'Статус:',
    'enabled' => 'Включено',
    'disabled' => 'Отключено',
    'view' => 'Вид:',
    'textual' => 'Текстовый',
    'simple' => 'Простой',
    'layouts' => 'Показывать в макетах',
    'layouts_help' => 'предложенные макеты выделены <b>жирным</b>',
    'featured_label' => 'Показывать в модуле «Рекомендуемые»',
    'clock_label' => 'Часы:',
    'clock_browser_label' => 'Браузер',
    'clock_browser_help' => 'Использовать часы операционной системы пользователя.',
    'clock_server_label' => 'Сервер',
    'clock_server_help' => 'Передать серверное время в браузер. Это может быть полезным, если в пользователя неверно настоено время в операционной системе, но точность этого метода невысокая и пользователь может запутаться.',
    'clock_combo_label' => 'Комбо',
    'clock_combo_help' => 'Использовать серверное время только если разница с браузерным временем очень большая.',
    'clock_critdiff' => 'Критическая разница (с):',
    'adv_setting' => 'расширенная настройка',
);

$_['error'] = array(
    'permission' => 'У вас нет прав для изменения модуля Neat Countdown.',
    'name' => 'Имя обязательно и должно состоять из 1&#8211;200 допустимых символов.',
    'status' => 'Статус обязателен и должен быть 0 или 1.',
    'view' => 'Вид отсчета обязателен и должен быть правильным.',
    'layouts' => 'Макеты обязательны и должны быть правильными.',
    'clock' => 'Часы обязательны и должны быть правильными.',
    'clock--critdiff' => 'Критическая разница должна быть позитивным и не слишком огромным целым числом.',
);

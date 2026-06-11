<?php

$_['heading_title'] = 'Countdown';

$_['textual_js'] = array(
    '_code' => 'en',
    // This format is self-invented and quite fragile. Test carefully.
    'view_active' => 'Time left: {days}&nbsp;day{day_number::s}&nbsp;{hours}:{minutes}:{seconds}',
    'view_expired' => 'The special offer is already expired.',
);

$_['simple'] = array(
    'text' => 'Time left:',
    'text_expired' => 'The special expired',
    'day_abbr' => 'd',

    'day_title' => 'days',
    'hour_title' => 'hours',
    'min_title' => 'minutes',
    'sec_title' => 'seconds',
);

<?php

$GLOBALS['TL_DCA']['tl_module']['fields']['room_event_archive'] = [
    'default' => '0',
    'exclude' => true,
    'inputType' => 'select',
    'foreignKey' => 'tl_calendar.title',
    'eval' => [
        'mandatory' => true,
        'tl_class' => 'clr w50'
    ],
    'sql' => "int(10) unsigned NOT NULL default '0'"
];

$GLOBALS['TL_DCA']['tl_module']['fields']['room_reservation_start_time'] = [
    'inputType' => 'select',
    'options_callback' => array(\Mindbird\Contao\RoomReservation\Dca\Module::class, 'optionsCallbackTimeslots'),
    'eval' => [
        'mandatory' => true,
        'tl_class' => 'w50'
    ],
    'sql' => "char(5) NOT NULL default '08:00'"
];

$GLOBALS['TL_DCA']['tl_module']['fields']['room_reservation_end_time'] = [
    'inputType' => 'select',
    'options_callback' => array(\Mindbird\Contao\RoomReservation\Dca\Module::class, 'optionsCallbackTimeslots'),
    'eval' => [
        'mandatory' => true,
        'tl_class' => 'w50'
    ],
    'sql' => "char(5) NOT NULL default '22:00'"
];

$GLOBALS['TL_DCA']['tl_module']['fields']['room_reservation_use_pricing'] = [
    'inputType' => 'checkbox',
    'eval' => ['tl_class' => 'clr m12 w50', 'submitOnChange' => true],
    'sql' => "char(1) NOT NULL default ''"
];

$GLOBALS['TL_DCA']['tl_module']['fields']['room_reservation_use_half_hour'] = [
    'inputType' => 'checkbox',
    'eval' => ['tl_class' => 'clr m12 w50', 'submitOnChange' => true],
    'sql' => "char(1) NOT NULL default ''"
];

$GLOBALS['TL_DCA']['tl_module']['fields']['room_reservation_price_half_hour'] = [
    'inputType' => 'text',
    'eval' => [
        'mandatory' => true,
        'tl_class' => 'w50',
        'rgxp' => 'digit'
    ],
    'sql' => "int(10) unsigned NOT NULL default '0'"
];

$GLOBALS['TL_DCA']['tl_module']['fields']['room_reservation_price_hour'] = [
    'inputType' => 'text',
    'eval' => [
        'mandatory' => true,
        'tl_class' => 'clr w50',
        'rgxp' => 'digit'
    ],
    'sql' => "int(10) unsigned NOT NULL default '0'"
];


$GLOBALS['TL_DCA']['tl_module']['fields']['room_reservation_use_half_day'] = [
    'inputType' => 'checkbox',
    'eval' => ['tl_class' => 'clr m12 w50', 'submitOnChange' => true],
    'sql' => "char(1) NOT NULL default ''"
];

$GLOBALS['TL_DCA']['tl_module']['fields']['room_reservation_price_half_day'] = [
    'inputType' => 'text',
    'eval' => [
        'mandatory' => true,
        'tl_class' => 'w50',
        'rgxp' => 'digit'
    ],
    'sql' => "int(10) unsigned NOT NULL default '0'"
];

$GLOBALS['TL_DCA']['tl_module']['fields']['room_reservation_price_day'] = [
    'inputType' => 'text',
    'eval' => [
        'mandatory' => true,
        'tl_class' => 'w50',
        'rgxp' => 'digit'
    ],
    'sql' => "int(10) unsigned NOT NULL default '0'"
];

$GLOBALS['TL_DCA']['tl_module']['fields']['room_reservation_use_evening'] = [
    'inputType' => 'checkbox',
    'eval' => ['tl_class' => 'clr m12 w50', 'submitOnChange' => true],
    'sql' => "char(1) NOT NULL default ''"
];

$GLOBALS['TL_DCA']['tl_module']['fields']['room_reservation_price_evening'] = [
    'inputType' => 'text',
    'eval' => [
        'mandatory' => true,
        'tl_class' => 'clr w50',
        'rgxp' => 'digit'
    ],
    'sql' => "int(10) unsigned NOT NULL default '0'"
];

$GLOBALS['TL_DCA']['tl_module']['fields']['room_reservation_evening_start'] = [
    'inputType' => 'select',
    'options' => $timeslot,
    'eval' => [
        'mandatory' => true,
        'tl_class' => 'w50'
    ],
    'sql' => "char(5) NOT NULL default '18:00'"
];

$GLOBALS['TL_DCA']['tl_module']['fields']['room_reservation_time_between_entries'] = [
    'inputType' => 'text',
    'eval' => [
        'mandatory' => true,
        'tl_class' => 'clr w50',
        'rgxp' => 'digit'
    ],
    'sql' => "int(10) unsigned NOT NULL default '0'"
];

$GLOBALS['TL_DCA']['tl_module']['fields']['room_reservation_min_booking_time'] = [
    'inputType' => 'text',
    'eval' => [
        'mandatory' => true,
        'tl_class' => 'w50',
        'rgxp' => 'digit'
    ],
    'sql' => "int(10) unsigned NOT NULL default '0'"
];

$GLOBALS['TL_DCA']['tl_module']['fields']['room_reservation_jump_to'] = [
    'inputType' => 'pageTree',
    'eval' => [
        'mandatory' => true,
        'tl_class' => 'clr',
        'fieldType' => 'radio',
    ],
    'sql' => "blob NULL"
];

$GLOBALS['TL_DCA']['tl_module']['fields']['room_reservation_page_agb'] = [
    'inputType' => 'pageTree',
    'eval' => [
        'mandatory' => true,
        'tl_class' => 'clr',
        'fieldType' => 'radio',
    ],
    'sql' => "blob NULL"
];

$GLOBALS['TL_DCA']['tl_module']['fields']['room_reservation_notification'] = [
    'inputType' => 'select',
    'foreignKey' => 'tl_nc_notification.title',
    'eval' => ['includeBlankOption' => true, 'chosen' => true, 'tl_class' => 'clr'],
    'sql' => "int(10) unsigned NOT NULL default '0'"
    ];

$GLOBALS['TL_DCA']['tl_module']['fields']['room_reservation_booking_one_day'] = [
    'inputType' => 'checkbox',
    'eval' => ['tl_class' => 'm12 w50'],
    'sql' => "char(1) NOT NULL default ''"
    ];

$GLOBALS['TL_DCA']['tl_module']['palettes']['__selector__'][] = 'room_reservation_use_pricing';
$GLOBALS['TL_DCA']['tl_module']['palettes']['__selector__'][] = 'room_reservation_use_half_hour';
$GLOBALS['TL_DCA']['tl_module']['palettes']['__selector__'][] = 'room_reservation_use_half_day';
$GLOBALS['TL_DCA']['tl_module']['palettes']['__selector__'][] = 'room_reservation_use_evening';

$GLOBALS['TL_DCA']['tl_module']['palettes']['room_reservation_booking'] = '{title_legend},name,headline,type;{archiv_legend},room_event_archive,room_reservation_booking_one_day,room_reservation_start_time,room_reservation_end_time,room_reservation_time_between_entries,room_reservation_min_booking_time,room_reservation_use_pricing,room_reservation_jump_to,room_reservation_page_agb,room_reservation_notification;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space';
$GLOBALS['TL_DCA']['tl_module']['subpalettes']['room_reservation_use_pricing'] = 'room_reservation_price_hour,room_reservation_price_day,room_reservation_use_half_hour,room_reservation_use_half_day,room_reservation_use_evening';
$GLOBALS['TL_DCA']['tl_module']['subpalettes']['room_reservation_use_half_hour'] = 'room_reservation_price_half_hour';
$GLOBALS['TL_DCA']['tl_module']['subpalettes']['room_reservation_use_half_day'] = 'room_reservation_price_half_day';
$GLOBALS['TL_DCA']['tl_module']['subpalettes']['room_reservation_use_evening'] = 'room_reservation_price_evening,room_reservation_evening_start';

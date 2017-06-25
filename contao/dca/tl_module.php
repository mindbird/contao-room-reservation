<?php

$GLOBALS['TL_DCA']['tl_module']['fields']['room_event_archive'] = array(
    'label' => &$GLOBALS['TL_LANG']['tl_module']['room_event_archive'],
    'default' => '',
    'exclude' => true,
    'inputType' => 'select',
    'foreignKey' => 'tl_calendar.title',
    'eval' => array(
        'mandatory' => true,
        'tl_class' => 'clr'
    ),
    'sql' => "int(10) unsigned NOT NULL default '0'"
);

$GLOBALS['TL_DCA']['tl_module']['fields']['room_reservation_start_time'] = array(
    'label' => &$GLOBALS['TL_LANG']['tl_module']['room_reservation_start_time'],
    'inputType' => 'text',
    'eval' => array(
        'mandatory' => true,
        'tl_class' => 'w50',
        'rgxp' => 'digit'
    ),
    'sql' => "int(2) unsigned NOT NULL default '0'"
);

$GLOBALS['TL_DCA']['tl_module']['fields']['room_reservation_end_time'] = array(
    'label' => &$GLOBALS['TL_LANG']['tl_module']['room_reservation_end_time'],
    'inputType' => 'text',
    'eval' => array(
        'mandatory' => true,
        'tl_class' => 'w50',
        'rgxp' => 'digit'
    ),
    'sql' => "int(2) unsigned NOT NULL default '0'"
);

$GLOBALS['TL_DCA']['tl_module']['fields']['room_reservation_price_half_hour'] = array(
    'label' => &$GLOBALS['TL_LANG']['tl_module']['room_reservation_price_half_hour'],
    'inputType' => 'text',
    'eval' => array(
        'mandatory' => true,
        'tl_class' => 'w50',
        'rgxp' => 'digit'
    ),
    'sql' => "int(10) unsigned NOT NULL default '0'"
);

$GLOBALS['TL_DCA']['tl_module']['fields']['room_reservation_price_hour'] = array(
    'label' => &$GLOBALS['TL_LANG']['tl_module']['room_reservation_price_hour'],
    'inputType' => 'text',
    'eval' => array(
        'mandatory' => true,
        'tl_class' => 'w50',
        'rgxp' => 'digit'
    ),
    'sql' => "int(10) unsigned NOT NULL default '0'"
);

$GLOBALS['TL_DCA']['tl_module']['fields']['room_reservation_price_half_day'] = array(
    'label' => &$GLOBALS['TL_LANG']['tl_module']['room_reservation_price_half_day'],
    'inputType' => 'text',
    'eval' => array(
        'mandatory' => true,
        'tl_class' => 'w50',
        'rgxp' => 'digit'
    ),
    'sql' => "int(10) unsigned NOT NULL default '0'"
);

$GLOBALS['TL_DCA']['tl_module']['fields']['room_reservation_price_day'] = array(
    'label' => &$GLOBALS['TL_LANG']['tl_module']['room_reservation_price_day'],
    'inputType' => 'text',
    'eval' => array(
        'mandatory' => true,
        'tl_class' => 'w50',
        'rgxp' => 'digit'
    ),
    'sql' => "int(10) unsigned NOT NULL default '0'"
);


$GLOBALS['TL_DCA']['tl_module']['palettes']['room_reservation'] = '{title_legend},name,headline,type;{archiv_legend},room_event_archive,jumpTo,room_reservation_start_time,room_reservation_end_time,room_reservation_price_half_hour,room_reservation_price_hour,room_reservation_price_half_day,room_reservation_price_day;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space';
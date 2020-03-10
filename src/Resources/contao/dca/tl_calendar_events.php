<?php

use Mindbird\Contao\RoomReservation\Dca\CalendarEvents;

$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['member'] = array(
    'label' => &$GLOBALS['TL_LANG']['tl_calendar_events']['member'],
    'default' => '0',
    'exclude' => true,
    'inputType' => 'select',
    'foreignKey' => 'tl_member.CONCAT(firstname," ",lastname)',
    'eval' => array(
        'tl_class' => 'clr'
    ),
    'relation' =>
        array('type' => 'hasOne', 'load' => 'lazy'),
    'sql' => "int(10) unsigned NOT NULL default '0'"
);

$GLOBALS['TL_DCA']['tl_calendar_events']['list']['sorting']['child_record_callback'] = array(CalendarEvents::class, 'listEvents');

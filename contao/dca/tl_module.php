<?php

$GLOBALS['TL_DCA']['tl_module']['fields']['room_event_archive'] = array(
    'label' => &$GLOBALS['TL_LANG']['tl_module']['room_event_archive'],
    'default' => '',
    'exclude' => true,
    'inputType' => 'select',
    'foreignKey' => 'tl_calendar_archive.title',
    'eval' => array(
        'mandatory' => true,
        'tl_class' => 'clr'
    ),
    'sql' => "int(10) unsigned NOT NULL default '0'"
);

$GLOBALS['TL_DCA']['tl_module']['palettes']['room_reservation'] = '{title_legend},name,headline,type;{archiv_legend},room_event_archive;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space';
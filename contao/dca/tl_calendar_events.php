<?php

$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['member'] = array(
    'label' => &$GLOBALS['TL_LANG']['tl_calendar_events']['member'],
    'default' => '',
    'exclude' => true,
    'inputType' => 'select',
    'foreignKey' => 'tl_member.CONCAT(firstname," ",lastname)',
    'eval' => array(
        'mandatory' => true,
        'tl_class' => 'clr'
    ),
    'relation' =>
        array('type'=>'hasOne', 'load'=>'lazy'),
    'sql' => "int(10) unsigned NOT NULL default '0'"
);



$GLOBALS['TL_DCA']['tl_calendar_events']['list']['sorting']['child_record_callback'] = array('tl_calendar_events_room_reservation', 'listEvents');

class tl_calendar_events_room_reservation extends tl_calendar_events {
    public function listEvents($row)
    {
        if ($row['member']) {
            $member = MemberModel::findByPk($row['member']);
            return parent::listEvents($row) . ' ' . $member->firstname . ' ' . $member->lastname;
        } else {
            return parent::listEvents($row);
        }

    }
}

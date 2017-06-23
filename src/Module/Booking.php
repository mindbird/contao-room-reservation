<?php

namespace RoomReservation\Module;

use Contao\CalendarEventsModel;
use Contao\FormHidden;
use Contao\FormTextField;
use Contao\FrontendUser;
use Contao\Module;
use Contao\Input;

/**
 * Created by mindbird
 * User: Florian Otto
 * Date: 22.06.17
 * Time: 13:26
 */
class Booking extends Module
{
    protected $strTemplate = 'mod_room_reservation';

    protected $fields = array();

    public function generate()
    {
        $GLOBALS['TL_CSS'][] = 'system/modules/room_reservation/assets/css/datepicker.min.css';
        $GLOBALS['TL_JAVASCRIPT'][] = 'system/modules/room_reservation/assets/js/datepicker.min.js';
        $GLOBALS['TL_JAVASCRIPT'][] = 'system/modules/room_reservation/assets/js/datepicker-de.js';
        $GLOBALS['TL_JAVASCRIPT'][] = 'system/modules/room_reservation/assets/js/jquery.validate.js';
        $GLOBALS['TL_JAVASCRIPT'][] = 'system/modules/room_reservation/assets/js/jquery.validate.de.js';

        return parent::generate();
    }

    public function compile()
    {
       $this->initFields();
       $user = FrontendUser::getInstance();

       if (Input::post($this->fields['formSubmit']->name) == $this->fields['formSubmit']->value) {
           $startDate = \DateTime::createFromFormat('d.m.Yh:s', Input::post('startDate'). Input::post('startTime'));
           $endDate = \DateTime::createFromFormat('d.m.Yh:s', Input::post('endDate'). Input::post('endTime'));
           //$result = $db->prepare("SELECT id FROM tl_calendar_events WHERE startDate <= ? AND endDate >= ?)")->execute($endDate, $startDate);
           $cem = new CalendarEventsModel();
           $cem->pid = $this->room_event_archive;
           $cem->startDate = $startDate->format('U');
           $cem->startTime = $startDate->format('U');
           $cem->endDate = $endDate->format('U');
           $cem->endTime = $endDate->format('U');
           $cem->title = $user->firstname . ' ' . $user->lastname;
           $cem->published = true;
           $cem->save();

           $this->jumpToOrReload($this->jumpTo);
       }
    }

    protected function initFields()
    {
        $field = new FormHidden();
        $field->name = 'FORM_SUBMIT';
        $field->value = 'room_reservation_booking_' . $this->id;
        $this->fields['formSubmit'] = $field;

        $field = new FormTextField();
        $field->template = 'form_room_reservation_textfield';
        $field->name = 'startDate';
        $field->label = 'Startdatum';
        $field->mandatory = true;
        $this->fields['startDate'] = $field;

        $field = new FormTextField();
        $field->addAttribute('data-rule-time', true);
        $field->type = 'time';
        $field->template = 'form_room_reservation_textfield';
        $field->name = 'startTime';
        $field->label = 'Startzeit';
        $field->mandatory = true;
        $this->fields['startTime'] = $field;

        $field = new FormTextField();
        $field->template = 'form_room_reservation_textfield';
        $field->name = 'endDate';
        $field->label = 'Enddatum';
        $field->mandatory = true;
        $this->fields['endDate'] = $field;

        $field = new FormTextField();
        $field->addAttribute('data-rule-time', true);
        $field->template = 'form_room_reservation_textfield';
        $field->name = 'endTime';
        $field->label = 'Endzeit';
        $field->mandatory = true;
        $this->fields['endTime'] = $field;

        $this->Template->fields = $this->fields;
    }
}
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
           $cem->startDate = $startDate->format('U');
           $cem->endDate = $endDate->format('U');
           $cem->title = $user->firstname . ' ' . $user->lastname;
           $cem->save();
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
        $field->name = 'dateStart';
        $field->label = 'Startdatum';
        $field->required = true;
        $this->fields['dateStart'] = $field;

        $field = new FormTextField();
        $field->template = 'form_room_reservation_textfield';
        $field->name = 'timeStart';
        $field->label = 'Startzeit';
        $field->required = true;
        $this->fields['timeStart'] = $field;

        $field = new FormTextField();
        $field->template = 'form_room_reservation_textfield';
        $field->name = 'dateEnd';
        $field->label = 'Enddatum';
        $field->required = true;
        $this->fields['dateEnd'] = $field;

        $field = new FormTextField();
        $field->template = 'form_room_reservation_textfield';
        $field->name = 'timeEnd';
        $field->label = 'Endzeit';
        $field->required = true;
        $this->fields['timeEnd'] = $field;

        $this->Template->fields = $this->fields;
    }
}
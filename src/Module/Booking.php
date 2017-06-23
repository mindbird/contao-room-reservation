<?php

namespace RoomReservation\Module;

/**
 * Created by mindbird
 * User: Florian Otto
 * Date: 22.06.17
 * Time: 13:26
 */
class Booking extends \Contao\Module
{

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
    }

    protected function initFields()
    {
        $field = new \Contao\FormTextField();
        $field->template = 'form_room_reservation_textfield';
        $field->name = 'dateStart';
        $field->label = 'Startdatum';
        $this->fields['dateStart'] = $field;

        $field = new \Contao\FormTextField();
        $field->template = 'form_room_reservation_textfield';
        $field->name = 'timeStart';
        $field->label = 'Startzeit';
        $this->fields['timeStart'] = $field;

        $field = new \Contao\FormTextField();
        $field->template = 'form_room_reservation_textfield';
        $field->name = 'dateEnd';
        $field->label = 'Enddatum';
        $this->fields['dateEnd'] = $field;

        $field = new \Contao\FormTextField();
        $field->template = 'form_room_reservation_textfield';
        $field->name = 'timeEnd';
        $field->label = 'Endzeit';
        $this->fields['timeEnd'] = $field;
    }
}
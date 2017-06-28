<?php

namespace RoomReservation\Module;

use Contao\CalendarEventsModel;
use Contao\Database;
use Contao\FormHidden;
use Contao\FormSelectMenu;
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
        $GLOBALS['TL_JAVASCRIPT'][] = 'system/modules/room_reservation/assets/js/datepicker.min.js|static';
        $GLOBALS['TL_JAVASCRIPT'][] = 'system/modules/room_reservation/assets/js/datepicker-de.js|static';
        $GLOBALS['TL_JAVASCRIPT'][] = 'system/modules/room_reservation/assets/js/jquery.validate.js|static';

        return parent::generate();
    }

    public function compile()
    {
        $this->initFields();
        $user = FrontendUser::getInstance();
        $db = Database::getInstance();

        if (Input::post($this->fields['formSubmit']->name) == $this->fields['formSubmit']->value) {
            $startDate = \DateTime::createFromFormat('d.m.YH:s', Input::post('startDate') . Input::post('startTime'));
            $endDate = \DateTime::createFromFormat('d.m.YH:s', Input::post('endDate') . Input::post('endTime'));
            $result = $db->prepare("SELECT id FROM tl_calendar_events WHERE startDate <= ? AND endDate >= ? AND pid = ?")->execute($endDate->format('U') + 30 * 60,
                $startDate->format('U'), $this->room_event_archive);
            if ($result->numRows == 0) {
                $cem = new CalendarEventsModel();
                $cem->pid = $this->room_event_archive;
                $cem->startDate = $startDate->format('U');
                $cem->startTime = $startDate->format('U');
                $cem->endDate = $endDate->format('U');
                $cem->endTime = $endDate->format('U');
                $cem->title = $user->firstname . ' ' . $user->lastname;
                $cem->published = true;
                $cem->addTime = true;
                $cem->save();

                $this->jumpToOrReload($this->jumpTo);
            } else {
                $this->Template->noTimeslotAvailable = true;
            }

        } elseif (Input::get('date') != '') {
            $date = substr(Input::get('date'), 6, 2) . '.' . substr(Input::get('date'), 4,
                    2) . '.' . substr(Input::get('date'), 0, 4);
            $this->fields['startDate']->value = $date;
            $this->fields['endDate']->value = $date;
        }

        $this->Template->priceDay = $this->room_reservation_price_day;
        $this->Template->priceHalfDay = $this->room_reservation_price_half_day;
        $this->Template->priceHour = $this->room_reservation_price_hour;
        $this->Template->priceHalfHour = $this->room_reservation_price_half_hour;
        $this->Template->startTime = $this->room_reservation_start_time;
        $this->Template->endTime = $this->room_reservation_end_time;
    }

    protected function initFields()
    {

        $timeslot = array();
        for ($i = $this->room_reservation_start_time; $i <= $this->room_reservation_end_time; $i++) {
            $timeslot[] = array(
                'label' => str_pad($i, 2, 0, STR_PAD_LEFT) . ':00',
                'value' => str_pad($i, 2, 0, STR_PAD_LEFT) . ':00'
            );
            if ($i != $this->room_reservation_end_time) {
                $timeslot[] = array(
                    'label' => str_pad($i, 2, 0, STR_PAD_LEFT) . ':30',
                    'value' => str_pad($i, 2, 0, STR_PAD_LEFT) . ':30'
                );
            }
        }

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

        $field = new FormSelectMenu();
        $field->type = 'time';
        //$field->template = 'form_room_reservation_textfield';
        $field->name = 'startTime';
        $field->label = 'Startzeit';
        $field->mandatory = true;
        $field->options = $timeslot;
        $this->fields['startTime'] = $field;

        $field = new FormTextField();
        $field->template = 'form_room_reservation_textfield';
        $field->name = 'endDate';
        $field->label = 'Enddatum';
        $field->mandatory = true;
        $this->fields['endDate'] = $field;

        $field = new FormSelectMenu();
        $field->addAttribute('data-rule-time', 'true');
        //$field->template = 'form_room_reservation_textfield';
        $field->name = 'endTime';
        $field->label = 'Endzeit';
        $field->mandatory = true;
        $field->options = $timeslot;
        $this->fields['endTime'] = $field;

        $this->Template->fields = $this->fields;
    }
}
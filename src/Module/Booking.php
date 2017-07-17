<?php

namespace RoomReservation\Module;

use Contao\CalendarEventsModel;
use Contao\Database;
use Contao\FormCheckBox;
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
            $startDate = \DateTime::createFromFormat('d.m.YH:i', Input::post('startDate') . Input::post('startTime'));
            $endDate = \DateTime::createFromFormat('d.m.YH:i', Input::post('endDate') . Input::post('endTime'));
            $result = $db->prepare("SELECT id FROM tl_calendar_events WHERE startTime <= ? AND endTime >= ? AND pid = ?")->execute($endDate->format('U') + $this->room_reservation_time_between_entries * 60,
                $startDate->format('U'), $this->room_event_archive);
            if ($result->numRows == 0) {
                $cem = new CalendarEventsModel();
                $cem->pid = $this->room_event_archive;
                $cem->startDate = $startDate->format('U');
                $cem->startTime = $startDate->format('U');
                $cem->endDate = $endDate->format('U');
                $cem->endTime = $endDate->format('U');
                $cem->title = Input::post('eventTitle');
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
        $this->Template->minBookingTime = $this->room_reservation_min_booking_time;
    }

    protected function initFields()
    {

        $timeslot = array();
        for ($i = $this->room_reservation_start_time; $i <= $this->room_reservation_end_time; $i++) {
            $timeslot[] = $this->addTimeslotarray($i, '00');
            $timeslot[] = $this->addTimeslotarray($i, '15');
            $timeslot[] = $this->addTimeslotarray($i, '30');
            if ($i != $this->room_reservation_end_time) {
                $timeslot[] = $this->addTimeslotarray($i, '45');
            }
        }

        $field = new FormHidden();
        $field->name = 'FORM_SUBMIT';
        $field->value = 'room_reservation_booking_' . $this->id;
        $this->fields['formSubmit'] = $field;

        $field = new FormTextField();
        $field->name = 'eventTitle';
        $field->label = 'Titel der Veranstaltung';
        $field->value = Input::post('startDate');
        $this->fields['eventTitle'] = $field;

        $field = new FormTextField();
        $field->template = 'form_room_reservation_textfield';
        $field->name = 'startDate';
        $field->label = 'Startdatum';
        $field->mandatory = true;
        $field->value = Input::post('startDate');
        $this->fields['startDate'] = $field;

        $field = new FormSelectMenu();
        $field->template = 'form_room_reservation_select';
        $field->name = 'startTime';
        $field->label = 'Startzeit';
        $field->mandatory = true;
        $field->options = $timeslot;
        $field->value = Input::post('startTime');
        $this->fields['startTime'] = $field;

        $field = new FormTextField();
        $field->template = 'form_room_reservation_textfield';
        $field->name = 'endDate';
        $field->label = 'Enddatum';
        $field->mandatory = true;
        $field->value = Input::post('endDate');
        $this->fields['endDate'] = $field;

        $field = new FormSelectMenu();
        $field->template = 'form_room_reservation_select';
        $field->name = 'endTime';
        $field->label = 'Endzeit';
        $field->mandatory = true;
        $field->options = $timeslot;
        $field->value = Input::post('endTime');
        $this->fields['endTime'] = $field;

        $pageAgbModel = \PageModel::findByPk($this->room_reservation_page_agb);
        if ($pageAgbModel) {
            $pageAgb = self::generateFrontendUrl($pageAgbModel->row());
            $label = 'Hiermit stimme ich den <a href="' . $pageAgb . '" target="_blank">AGB</a> zu';
        } else {
            $label = 'Hiermit stimme ich den AGB zu';
        }
        $field = new FormCheckBox();
        $field->name = 'agb';
        $field->value = Input::post('agb');
        $field->options = array(
            array('value' => 'Hiermit stimme ich den AGB zu', 'label' => $label)
        );
        $this->fields['agb'] = $field;

        $this->Template->fields = $this->fields;
    }

    private function addTimeslotarray($hour, $minute)
    {
        return array(
            'label' => str_pad($hour, 2, 0, STR_PAD_LEFT) . ':' . $minute,
            'value' => str_pad($hour, 2, 0, STR_PAD_LEFT) . ':' . $minute
        );
    }
}
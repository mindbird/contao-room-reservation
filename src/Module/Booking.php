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
use NotificationCenter\Model\Notification;

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
        if (Input::post('action') == 'checkAvailability' && Input::post($this->fields['formSubmit']->name) == $this->fields['formSubmit']->value) {
            print json_encode($this->checkAvailabilityAjax(Input::post('repeat'), Input::post('startDate'), Input::post('startTime'), Input::post('endDate'), Input::post('endTime')));
            die();
        }

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

        if (Input::post($this->fields['formSubmit']->name) == $this->fields['formSubmit']->value) {
            $repeat = 0;
            if (Input::post('repeatTimes') > 0) {
                $repeat = Input::post('repeatTimes');
            }

            for ($i = 0; $i <= $repeat; $i++) {
                $addInterval = new \DateInterval('P' . $i * 7 . 'D');
                $startDate = \DateTime::createFromFormat('d.m.YH:i',Input::post('startDate') . Input::post('startTime'));
                $endDate = \DateTime::createFromFormat('d.m.YH:i', Input::post('endDate') . Input::post('endTime'));
                $startDate->add($addInterval);
                $endDate->add($addInterval);

                if ($this->checkAvailability($startDate, $endDate)) {
                    $cem = new CalendarEventsModel();
                    $cem->pid = $this->room_event_archive;
                    $cem->startDate = $startDate->format('U');
                    $cem->startTime = $startDate->format('U');
                    $cem->endDate = $endDate->format('U');
                    $cem->endTime = $endDate->format('U');
                    $cem->title = Input::post('eventTitle');
                    $cem->published = true;
                    $cem->addTime = true;
                    $cem->member = $user->id;
                    $cem->save();
                }
            }

            if ($this->room_reservation_notification != 0) {
                $startDate = \DateTime::createFromFormat('d.m.YH:i',Input::post('startDate') . Input::post('startTime'));
                $endDate = \DateTime::createFromFormat('d.m.YH:i', Input::post('endDate') . Input::post('endTime'));
                $token = [
                    'room_start_date' => $startDate->format($GLOBALS['TL_CONFIG']['datimFormat']),
                    'room_end_date' => $endDate->format($GLOBALS['TL_CONFIG']['datimFormat']),
                    'room_repeat' => $repeat > 0 ? true : false,
                    'room_repeat_times' => $repeat,
                    'room_event_title' => Input::post('eventTitle'),
                ];
                $notification = Notification::findByPk($this->room_reservation_notification);
                if (null !== $notification) {
                    $notification->send($token);
                }
            }

            $this->jumpToOrReload($this->jumpTo, '/month/' . $startDate->format('Ym'));

        } elseif (Input::get('date') != '') {
            $date = substr(Input::get('date'), 6, 2) . '.' . substr(Input::get('date'), 4,
                    2) . '.' . substr(Input::get('date'), 0, 4);
            $this->fields['startDate']->value = $date;
            $this->fields['endDate']->value = $date;
        }

        $this->Template->usePricing = $this->room_reservation_use_pricing;
        $this->Template->priceDay = $this->room_reservation_price_day;
        $this->Template->priceHalfDay = $this->room_reservation_price_half_day;
        $this->Template->priceHour = $this->room_reservation_price_hour;
        $this->Template->priceHalfHour = $this->room_reservation_price_half_hour;
        $this->Template->startTime = $this->room_reservation_start_time;
        $this->Template->endTime = $this->room_reservation_end_time;
        $this->Template->minBookingTime = $this->room_reservation_min_booking_time;
        $this->Template->useHalfHour = $this->room_reservation_use_half_hour;
        $this->Template->useHalfDay = $this->room_reservation_use_half_day;
        $this->Template->useEvening = $this->room_reservation_use_evening;
        $this->Template->priceEvening = $this->room_reservation_price_evening;
        $eveningStart = new \DateTime('@' . $this->room_reservation_evening_start, new \DateTimeZone($GLOBALS['TL_CONFIG']['timeZone']));
        $this->Template->eveningStart = $eveningStart->format('H:i');
        if($this->room_reservation_booking_one_day == '1') {
            $this->fields['endDate']->template = 'form_hidden';
        }
    }

    protected function initFields()
    {

        $timeslot = array();
        $startTime = new \DateTime('@' . $this->room_reservation_start_time, new \DateTimeZone($GLOBALS['TL_CONFIG']['timeZone']));
        $endTime = new \DateTime('@' . $this->room_reservation_end_time, new \DateTimeZone($GLOBALS['TL_CONFIG']['timeZone']));
        $time = $startTime;
        $interval = new \DateInterval('PT15M');
        while ($time <= $endTime) {
            $timeslot[] = [
                'label' => $time->format('H:i'),
                'value' => $time->format('H:i')
            ];
            $time->add($interval);
        }

        $field = new FormHidden();
        $field->name = 'FORM_SUBMIT';
        $field->value = 'room_reservation_booking_' . $this->id;
        $this->fields['formSubmit'] = $field;

        $field = new FormTextField();
        $field->template = 'form_room_reservation_textfield';
        $field->name = 'eventTitle';
        $field->id = 'eventTitle';
        $field->label = 'Titel der Veranstaltung';
        $field->value = Input::post('eventTitle');
        $this->fields['eventTitle'] = $field;

        $field = new FormTextField();
        $field->template = 'form_room_reservation_textfield';
        $field->name = 'startDate';
        $field->id = 'startDate';
        $field->label = 'Startdatum';
        $field->mandatory = true;
        $field->value = Input::post('startDate');
        $this->fields['startDate'] = $field;

        $field = new FormSelectMenu();
        $field->template = 'form_room_reservation_select';
        $field->name = 'startTime';
        $field->id = 'startTime';
        $field->label = 'Startzeit';
        $field->mandatory = true;
        $field->options = $timeslot;
        $field->value = Input::post('startTime');
        $this->fields['startTime'] = $field;

        $field = new FormTextField();
        $field->template = 'form_room_reservation_textfield';
        $field->name = 'endDate';
        $field->id = 'endDate';
        $field->label = 'Enddatum';
        $field->mandatory = true;
        $field->value = Input::post('endDate');
        $this->fields['endDate'] = $field;

        $field = new FormSelectMenu();
        $field->template = 'form_room_reservation_select';
        $field->name = 'endTime';
        $field->id = 'endTime';
        $field->label = 'Endzeit';
        $field->mandatory = true;
        $field->options = $timeslot;
        $field->value = Input::post('endTime');
        $this->fields['endTime'] = $field;

        $field = new FormCheckBox();
        $field->template = 'form_room_reservation_checkbox';
        $field->name = 'repeat';
        $field->id = 'repeat';
        $field->value = Input::post('repeat');
        $field->options = array(
            array('value' => '1', 'label' => 'Soll der Termin wiederholt werden?', 'mandatory' => true)
        );
        $this->fields['repeat'] = $field;

        $field = new FormTextField();
        $field->template = 'form_room_reservation_textfield';
        $field->name = 'repeatTimes';
        $field->id = 'repeatTimes';
        $field->label = 'Wie viele Wochen soll der Termin wiederholt werden?';
        $field->mandatory = true;
        $field->value = Input::post('repeatTimes') > 0 ? Input::post('repeatTimes') : 0;
        $this->fields['repeatTimes'] = $field;

        $pageAgbModel = \PageModel::findByPk($this->room_reservation_page_agb);
        if ($pageAgbModel) {
            $pageAgb = self::generateFrontendUrl($pageAgbModel->row());
            $label = 'Hiermit stimme ich den <a href="' . $pageAgb . '" target="_blank">AGB</a> zu';
        } else {
            $label = 'Hiermit stimme ich den AGB zu';
        }
        $field = new FormCheckBox();
        $field->template = 'form_room_reservation_checkbox';
        $field->name = 'agb';
        $field->id = 'agb';
        $field->value = Input::post('agb');
        $field->options = array(
            array('value' => 'Hiermit stimme ich den AGB zu', 'label' => $label, 'mandatory' => true)
        );
        $field->mandatory = true;
        $this->fields['agb'] = $field;

        $this->Template->fields = $this->fields;
    }

    /**
     * @param \DateTime $startDate
     * @param \DateTime $endDate
     * @return boolean
     */
    private function checkAvailability($startDate, $endDate)
    {
        $db = Database::getInstance();
        $result = $db->prepare("SELECT id FROM tl_calendar_events WHERE startTime <= ? AND endTime >= ? AND pid = ?")->execute($endDate->format('U') + $this->room_reservation_time_between_entries * 60,
            $startDate->format('U'), $this->room_event_archive);

        return $result->numRows == 0;
    }

    private function checkAvailabilityAjax(
        $repeat,
        $startDate,
        $startTime,
        $endDate,
        $endTime
    ) {
        $return = [
            'status' => true,
            'msg' => '',
            'events' => []
        ];

        for ($i = 0; $i <= $repeat; $i++) {
            $addInterval = new \DateInterval('P' . $i * 7 . 'D');
            $startDateTime = \DateTime::createFromFormat('d.m.YH:i', $startDate . $startTime);
            $startDateTime->add($addInterval);
            $endDateTime = \DateTime::createFromFormat('d.m.YH:i', $endDate . $endTime);
            $endDateTime->add($addInterval);
            $availabilityEvent = '<tr><td>' . $startDateTime->format($GLOBALS['TL_CONFIG']['datimFormat']) . '</td><td>' . $endDateTime->format($GLOBALS['TL_CONFIG']['datimFormat']) . '</td><td class="price"><span class="value"></span>,00 EUR</td><td>';
            if (!$this->checkAvailability($startDateTime, $endDateTime)) {
                $availabilityEvent .= '<span class="error">nicht verfügbar</span>';
                $return['status'] = false;
            } else {
                $availabilityEvent .= '<span>verfügbar</span>';
            }
            $return['events'][] = $availabilityEvent . '</td>';
        }

        return $return;
    }
}
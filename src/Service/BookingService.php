<?php

/*
 * This file is part of [mindbird/contao-room-reservation].
 *
 * (c) mindbird
 *
 * @license LGPL-3.0-or-later
 */

namespace Mindbird\Contao\RoomReservation\Service;

use Contao\Database;
use Contao\FormCheckBox;
use Contao\FormHidden;
use Contao\FormSelectMenu;
use Contao\FormTextField;
use Contao\Input;
use Contao\PageModel;
use DateInterval;
use DateTime;
use Exception;
use Haste\Http\Response\JsonResponse;

class BookingService
{
    /**
     * @param $repeat
     * @param $startDate
     * @param $startTime
     * @param $endDate
     * @param $endTime
     * @param $roomEventArchiveId
     *
     * @throws Exception
     *
     * @return JsonResponse
     * @TODO Refactor return message
     */
    public function checkAvailabilityAjax(
        $repeat,
        $startDate,
        $startTime,
        $endDate,
        $endTime,
        $roomEventArchiveId
    ) {
        $events = [];

        for ($i = 0; $i <= $repeat; ++$i) {
            $addInterval = new DateInterval('P'.$i * 7 .'D');
            $startDateTime = DateTime::createFromFormat('d.m.YH:i', $startDate.$startTime);
            $startDateTime->add($addInterval);
            $endDateTime = DateTime::createFromFormat('d.m.YH:i', $endDate.$endTime);
            $endDateTime->add($addInterval);
            $availabilityEvent = '<tr><td>'.$startDateTime->format($GLOBALS['TL_CONFIG']['datimFormat']).'</td><td>'.$endDateTime->format($GLOBALS['TL_CONFIG']['datimFormat']).'</td><td class="price"><span class="value"></span>,00 EUR</td><td>';
            if (!$this->checkAvailability($startDateTime, $endDateTime, $roomEventArchiveId)) {
                $availabilityEvent .= '<span class="error">nicht verfügbar</span>';
                $return['status'] = false;
            } else {
                $availabilityEvent .= '<span>verfügbar</span>';
            }
            $events[] = $availabilityEvent.'</td>';
        }

        return new JsonResponse([
            'status' => true,
            'msg' => '',
            'events' => $events,
        ]);
    }

    /**
     * @param int $roomEventArchiveId
     *
     * @return bool
     */
    public function checkAvailability(DateTime $startDate, DateTime $endDate, $roomEventArchiveId)
    {
        $db = Database::getInstance();
        $result = $db->prepare('SELECT id FROM tl_calendar_events WHERE startTime <= ? AND endTime >= ? AND pid = ?')->execute(
            $endDate->format('U') + $this->room_reservation_time_between_entries * 60,
            $startDate->format('U'),
            $roomEventArchiveId
        );

        return 0 === $result->numRows;
    }

    public function initFields($moduleId, $startTime, $endTime, $minBookingTime, $pageAgbId) {

        $fields = [];

        $date = Input::get('date');
        if ('' !== $date) {
            $date = substr(Input::get('date'), 6, 2).'.'
                .substr(Input::get('date'), 4, 2).'.'
                .substr(Input::get('date'), 0, 4);
        }

        $field = new FormHidden();
        $field->name = 'FORM_SUBMIT';
        $field->value = 'room_reservation_booking_'.$moduleId;
        $fields['formSubmit'] = $field;

        $field = new FormTextField();
        $field->template = 'form_room_reservation_textfield';
        $field->name = 'eventTitle';
        $field->id = 'eventTitle';
        $field->label = 'Titel der Veranstaltung';
        $field->value = Input::post('eventTitle');
        $fields['eventTitle'] = $field;

        $field = new FormTextField();
        $field->template = 'form_room_reservation_textfield';
        $field->name = 'startDate';
        $field->id = 'startDate';
        $field->label = 'Startdatum';
        $field->mandatory = true;
        $field->value = '' === $date ? date('d.m.Y') : $date;
        $fields['startDate'] = $field;

        $timeslot = [];
        $startTime = new DateTime($startTime);
        $endTime = new DateTime($endTime);
        $endTime->sub(new DateInterval('PT'.$minBookingTime.'M'));
        $time = $startTime;
        $interval = new DateInterval('PT15M');
        while ($time <= $endTime) {
            $timeslot[] = [
                'label' => $time->format('H:i'),
                'value' => $time->format('H:i'),
            ];
            $time->add($interval);
        }

        $field = new FormSelectMenu();
        $field->template = 'form_room_reservation_select';
        $field->name = 'startTime';
        $field->id = 'startTime';
        $field->label = 'Startzeit';
        $field->mandatory = true;
        $field->options = $timeslot;
        $field->value = Input::post('startTime');
        $fields['startTime'] = $field;

        $field = new FormTextField();
        $field->template = 'form_room_reservation_textfield';
        $field->name = 'endDate';
        $field->id = 'endDate';
        $field->label = 'Enddatum';
        $field->mandatory = true;
        $field->value = '' === $date ? date('d.m.Y') : $date;
        $fields['endDate'] = $field;

        $field = new FormSelectMenu();
        $field->template = 'form_room_reservation_select';
        $field->name = 'endTime';
        $field->id = 'endTime';
        $field->label = 'Endzeit';
        $field->mandatory = true;
        $field->options = $timeslot;
        $field->value = Input::post('endTime');
        $fields['endTime'] = $field;

        $field = new FormCheckBox();
        $field->template = 'form_room_reservation_checkbox';
        $field->name = 'repeat';
        $field->id = 'repeat';
        $field->value = Input::post('repeat');
        $field->options = [
            ['value' => '1', 'label' => 'Soll der Termin wiederholt werden?', 'mandatory' => true],
        ];
        $fields['repeat'] = $field;

        $field = new FormTextField();
        $field->template = 'form_room_reservation_textfield';
        $field->name = 'repeatTimes';
        $field->id = 'repeatTimes';
        $field->label = 'Wie viele Wochen soll der Termin wiederholt werden?';
        $field->mandatory = true;
        $field->value = Input::post('repeatTimes') > 0 ? Input::post('repeatTimes') : 0;
        $fields['repeatTimes'] = $field;

        /** @var PageModel $pageAgbModel */
        $pageAgbModel = \PageModel::findByPk($pageAgbId);
        if ($pageAgbModel) {
            $pageAgb = $pageAgbModel->getFrontendUrl();
            $label = 'Hiermit stimme ich den <a href="'.$pageAgb.'" target="_blank">AGB</a> zu';
        } else {
            $label = 'Hiermit stimme ich den AGB zu';
        }
        $field = new FormCheckBox();
        $field->template = 'form_room_reservation_checkbox';
        $field->name = 'agb';
        $field->id = 'agb';
        $field->value = Input::post('agb');
        $field->options = [
            ['value' => 'Hiermit stimme ich den AGB zu', 'label' => $label, 'mandatory' => true],
        ];
        $field->mandatory = true;
        $fields['agb'] = $field;

        return $fields;
    }
}

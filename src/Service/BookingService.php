<?php

/*
 * This file is part of [mindbird/contao-room-reservation].
 *
 * (c) mindbird
 *
 * @license LGPL-3.0-or-later
 */

namespace Mindbird\Contao\RoomReservation\Service;

use Contao\FormCheckbox;
use Contao\FormHidden;
use Contao\FormSelect;
use Contao\FormText;
use Contao\Input;
use Contao\PageModel;
use Contao\CoreBundle\Routing\ContentUrlGenerator;
use DateInterval;
use DateTime;
use DateTimeInterface;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class BookingService
{
    /** @var EntityManagerInterface */
    protected $entityManager;
    /** @var ContentUrlGenerator */
    protected $contentUrlGenerator;

    public function __construct(EntityManagerInterface $entityManager, ContentUrlGenerator $contentUrlGenerator)
    {
        $this->entityManager = $entityManager;
        $this->contentUrlGenerator = $contentUrlGenerator;
    }

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
     * @TODO Refactor return message
     */
    public function checkAvailabilityAjax(
        int $repeat,
        string $startDate,
        string $startTime,
        string $endDate,
        string $endTime,
        int $roomEventArchiveId,
        int $timeBetweenEntries
    ): array
    {
        $events = [];
        $status = true;

        for ($i = 0; $i <= $repeat; ++$i) {
            $addInterval = new DateInterval('P'.(7 * $i).'D');
            $startDateTime = $this->createDateTime($startDate, $startTime);
            $startDateTime->add($addInterval);
            $endDateTime = $this->createDateTime($endDate, $endTime);
            $endDateTime->add($addInterval);
            $availabilityEvent = '<tr><td>'.$startDateTime->format($GLOBALS['TL_CONFIG']['datimFormat']).'</td><td>'.$endDateTime->format($GLOBALS['TL_CONFIG']['datimFormat']).'</td><td class="price"><span class="value"></span>,00 EUR</td><td>';
            if (!$this->checkAvailability($startDateTime, $endDateTime, $roomEventArchiveId, $timeBetweenEntries)) {
                $availabilityEvent .= '<span class="error">nicht verfügbar</span>';
                $status = false;
            } else {
                $availabilityEvent .= '<span>verfügbar</span>';
            }
            $events[] = $availabilityEvent.'</td>';
        }

        return [
            'status' => $status,
            'msg' => '',
            'events' => $events,
        ];
    }

    /**
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @param int $roomEventArchiveId
     *
     * @return bool
     */
    public function checkAvailability(DateTimeInterface $startDate, DateTimeInterface $endDate, int $roomEventArchiveId, int $timeBetweenEntries): bool
    {
        $result = $this->entityManager->getConnection()->executeQuery(
            'SELECT 1 FROM tl_calendar_events WHERE startTime <= :startTime AND endTime >= :endTime AND pid = :pid LIMIT 1',
            [
                'startTime' => (int) $endDate->format('U') + ($timeBetweenEntries * 60),
                'endTime' => (int) $startDate->format('U'),
                'pid' => $roomEventArchiveId,
            ]
        );

        return false === $result->fetchOne();
    }

    public function initFields($moduleId, $startTime, $endTime, $minBookingTime, $pageAgbId): array
    {

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

        $field = new FormText();
        $field->template = 'form_room_reservation_textfield';
        $field->name = 'eventTitle';
        $field->id = 'eventTitle';
        $field->label = 'Titel der Veranstaltung';
        $field->value = Input::post('eventTitle');
        $fields['eventTitle'] = $field;

        $field = new FormText();
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

        $field = new FormSelect();
        $field->template = 'form_room_reservation_select';
        $field->name = 'startTime';
        $field->id = 'startTime';
        $field->label = 'Startzeit';
        $field->mandatory = true;
        $field->options = $timeslot;
        $field->value = Input::post('startTime');
        $fields['startTime'] = $field;

        $field = new FormText();
        $field->template = 'form_room_reservation_textfield';
        $field->name = 'endDate';
        $field->id = 'endDate';
        $field->label = 'Enddatum';
        $field->mandatory = true;
        $field->value = '' === $date ? date('d.m.Y') : $date;
        $fields['endDate'] = $field;

        $field = new FormSelect();
        $field->template = 'form_room_reservation_select';
        $field->name = 'endTime';
        $field->id = 'endTime';
        $field->label = 'Endzeit';
        $field->mandatory = true;
        $field->options = $timeslot;
        $field->value = Input::post('endTime');
        $fields['endTime'] = $field;

        $field = new FormCheckbox();
        $field->template = 'form_room_reservation_checkbox';
        $field->name = 'repeat';
        $field->id = 'repeat';
        $field->value = Input::post('repeat');
        $field->options = [
            ['value' => '1', 'label' => 'Soll der Termin wiederholt werden?', 'mandatory' => true],
        ];
        $fields['repeat'] = $field;

        $field = new FormText();
        $field->template = 'form_room_reservation_textfield';
        $field->name = 'repeatTimes';
        $field->id = 'repeatTimes';
        $field->label = 'Wie viele Wochen soll der Termin wiederholt werden?';
        $field->mandatory = true;
        $field->value = Input::post('repeatTimes') > 0 ? Input::post('repeatTimes') : 0;
        $fields['repeatTimes'] = $field;

        /** @var PageModel $pageAgbModel */
        $pageAgbModel = PageModel::findByPk($pageAgbId);
        if ($pageAgbModel) {
            $pageAgb = $this->contentUrlGenerator->generate($pageAgbModel);
            $label = 'Hiermit stimme ich den <a href="'.$pageAgb.'" target="_blank">AGB</a> zu';
        } else {
            $label = 'Hiermit stimme ich den AGB zu';
        }
        $field = new FormCheckbox();
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

    private function createDateTime(string $date, string $time): DateTime
    {
        $dateTime = DateTime::createFromFormat('!d.m.Y H:i', $date.' '.$time);
        $errors = DateTime::getLastErrors();

        if (false === $dateTime || (false !== $errors && (0 !== $errors['warning_count'] || 0 !== $errors['error_count']))) {
            throw new Exception('Invalid reservation date or time.');
        }

        return $dateTime;
    }
}

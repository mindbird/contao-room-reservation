<?php

namespace Mindbird\Contao\RoomReservation\Dca;

use Contao\System;
use DateInterval;
use DateTime;
use Mindbird\Contao\RoomReservation\NotificationType\RoomReservationNotificationType;
use Terminal42\NotificationCenterBundle\NotificationCenter;

class Module
{
    public function optionsCallbackTimeslots()
    {
        $timeslot = [];
        $startTime = new DateTime('00:00');
        $endTime = new DateTime('23:59');
        $time = $startTime;
        $interval = new DateInterval('PT15M');
        while ($time <= $endTime) {
            $timeslot[] = $time->format('H:i');
            $time->add($interval);
        }

        return $timeslot;
    }

    public function optionsCallbackNotifications(): array
    {
        return System::getContainer()
            ->get(NotificationCenter::class)
            ->getNotificationsForNotificationType(RoomReservationNotificationType::NAME);
    }
}

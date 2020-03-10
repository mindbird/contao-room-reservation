<?php

namespace Mindbird\Contao\RoomReservation\Dca;

use DateInterval;
use DateTime;

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
}

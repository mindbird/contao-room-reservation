<?php

/*
 * This file is part of [mindbird/contao-room-reservation].
 *
 * (c) mindbird
 *
 * @license LGPL-3.0-or-later
 */

namespace Mindbird\Contao\RoomReservation;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class ContaoRoomReservationBundle extends Bundle
{
    public function getPath(): string
    {
        return dirname(__DIR__);
    }
}

<?php

namespace Mindbird\Contao\RoomReservation\Module;

use Contao\Module;

/**
 * Created by mindbird
 * User: Florian Otto
 * Date: 22.06.17
 * Time: 13:26
 */
class BookingController extends Module
{
    protected $strTemplate = 'mod_room_reservation';

    protected $fields = array();

    public function generate()
    {


        return parent::generate();
    }

    public function compile()
    {

    }


}

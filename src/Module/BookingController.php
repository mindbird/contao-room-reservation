<?php

namespace Mindbird\Contao\RoomReservation\Module;

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

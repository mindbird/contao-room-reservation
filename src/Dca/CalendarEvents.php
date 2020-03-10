<?php

/*
 * This file is part of [mindbird/contao-room-reservation].
 *
 * (c) mindbird
 *
 * @license LGPL-3.0-or-later
 */

namespace Mindbird\Contao\RoomReservation\Dca;

use Contao\Calendar;
use Contao\Config;
use Contao\Date;
use Contao\MemberModel;

class CalendarEvents
{
    /**
     * Add the type of input field.
     *
     * @param array $arrRow
     *
     * @return string
     */
    public function listEvents($arrRow)
    {
        $span = Calendar::calculateSpan($arrRow['startTime'], $arrRow['endTime']);

        if ($span > 0) {
            $date = Date::parse(Config::get(($arrRow['addTime'] ? 'datimFormat' : 'dateFormat')), $arrRow['startTime']).$GLOBALS['TL_LANG']['MSC']['cal_timeSeparator']. Date::parse(Config::get(($arrRow['addTime'] ? 'datimFormat' : 'dateFormat')), $arrRow['endTime']);
        } elseif ($arrRow['startTime'] === $arrRow['endTime']) {
            $date = Date::parse(Config::get('dateFormat'), $arrRow['startTime']).($arrRow['addTime'] ? ' '. Date::parse(Config::get('timeFormat'), $arrRow['startTime']) : '');
        } else {
            $date = Date::parse(Config::get('dateFormat'), $arrRow['startTime']).($arrRow['addTime'] ? ' '. Date::parse(Config::get('timeFormat'), $arrRow['startTime']).$GLOBALS['TL_LANG']['MSC']['cal_timeSeparator']. Date::parse(Config::get('timeFormat'), $arrRow['endTime']) : '');
        }

        if ($arrRow['member'] > 0) {
            $member = MemberModel::findByPk($arrRow['member']);
            if (null !== $member) {
                return '<div class="tl_content_left">'.$arrRow['title'].' <span style="color:#999;padding-left:3px">['.$date.']</span> '.$member->firstname.' '.$member->lastname.'</div>';
            }
        }

        return '<div class="tl_content_left">'.$arrRow['title'].' <span style="color:#999;padding-left:3px">['.$date.']</span></div>';
    }
}

<?php

/*
 * This file is part of [mindbird/contao-room-reservation].
 *
 * (c) mindbird
 *
 * @license LGPL-3.0-or-later
 */

namespace Mindbird\Contao\RoomReservation\Dca;

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
        $span = \Contao\Calendar::calculateSpan($arrRow['startTime'], $arrRow['endTime']);

        if ($span > 0) {
            $date = \Contao\Date::parse(\Contao\Config::get(($arrRow['addTime'] ? 'datimFormat' : 'dateFormat')), $arrRow['startTime']).$GLOBALS['TL_LANG']['MSC']['cal_timeSeparator'].\Contao\Date::parse(\Contao\Config::get(($arrRow['addTime'] ? 'datimFormat' : 'dateFormat')), $arrRow['endTime']);
        } elseif ($arrRow['startTime'] === $arrRow['endTime']) {
            $date = \Contao\Date::parse(\Contao\Config::get('dateFormat'), $arrRow['startTime']).($arrRow['addTime'] ? ' '.\Contao\Date::parse(\Contao\Config::get('timeFormat'), $arrRow['startTime']) : '');
        } else {
            $date = \Contao\Date::parse(\Contao\Config::get('dateFormat'), $arrRow['startTime']).($arrRow['addTime'] ? ' '.\Contao\Date::parse(\Contao\Config::get('timeFormat'), $arrRow['startTime']).$GLOBALS['TL_LANG']['MSC']['cal_timeSeparator'].\Contao\Date::parse(\Contao\Config::get('timeFormat'), $arrRow['endTime']) : '');
        }

        if ($arrRow['member'] > 0) {
            $member = \Contao\MemberModel::findByPk($arrRow['member']);
            if (null !== $member) {
                return '<div class="tl_content_left">'.$arrRow['title'].' <span style="color:#999;padding-left:3px">['.$date.']</span> '.$member->firstname.' '.$member->lastname.'</div>';
            }
        }

        return '<div class="tl_content_left">'.$arrRow['title'].' <span style="color:#999;padding-left:3px">['.$date.']</span></div>';
    }
}

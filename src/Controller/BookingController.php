<?php


namespace Contao\Mindbird\RoomReservation\Controller;

use Contao\CalendarEventsModel;
use Contao\CoreBundle\Controller\FrontendModule\AbstractFrontendModuleController;
use Contao\Environment;
use Contao\FormCheckBox;
use Contao\FormHidden;
use Contao\FormSelectMenu;
use Contao\FormTextField;
use Contao\Frontend;
use Contao\FrontendUser;
use Contao\Input;
use Contao\ModuleModel;
use Contao\PageModel;
use Contao\System;
use Contao\Template;
use Mindbird\Contao\RoomReservation\Service\BookingService;
use NotificationCenter\Model\Notification;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class BookingController extends AbstractFrontendModuleController
{
    protected $bookingService;

    public function __construct(BookingService $bookingService)
    {
        $this->bookingService = $bookingService;
    }

    protected function getResponse(Template $template, ModuleModel $model, Request $request): ?Response
    {
        if (Input::post('action') == 'checkAvailability' && Input::post($this->fields['formSubmit']->name) == $this->fields['formSubmit']->value) {
            return json_encode($this->checkAvailabilityAjax(Input::post('repeat'), Input::post('startDate'), Input::post('startTime'), Input::post('endDate'), Input::post('endTime')));
        }

        $GLOBALS['TL_CSS'][] = 'system/modules/room_reservation/assets/css/datepicker.min.css';
        $GLOBALS['TL_JAVASCRIPT'][] = 'system/modules/room_reservation/assets/js/datepicker.min.js|static';
        $GLOBALS['TL_JAVASCRIPT'][] = 'system/modules/room_reservation/assets/js/datepicker-de.js|static';
        $GLOBALS['TL_JAVASCRIPT'][] = 'system/modules/room_reservation/assets/js/jquery.validate.js|static';

        $template->fields = $this->bookingService->initFields(
            $model->id,
            $model->room_reservation_start_time,
            $model->room_reservation_end_time,
            $model->room_reservation_min_booking_time,
            $model->room_reservation_page_agb
        );
        $user = FrontendUser::getInstance();

        if (Input::post('room_reservation_booking_' . $model->id) === 'FORM_SUBMIT') {
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

                if ($this->bookingService->checkAvailability($startDate, $endDate, $model->room_event_archive)) {
                    $cem = new CalendarEventsModel();
                    $cem->pid = $model->room_event_archive;
                    $cem->startDate = $startDate->format('U');
                    $cem->startTime = $startDate->format('U');
                    $cem->endDate = $endDate->format('U');
                    $cem->endTime = $endDate->format('U');
                    $cem->title = Input::post('eventTitle');
                    $cem->published = true;
                    $cem->addTime = true;
                    $cem->member = $user !== null ? $user->id : null;
                    $cem->save();
                }
            }

            if ($model->room_reservation_notification != 0) {
                $startDate = \DateTime::createFromFormat('d.m.YH:i',Input::post('startDate') . Input::post('startTime'));
                $endDate = \DateTime::createFromFormat('d.m.YH:i', Input::post('endDate') . Input::post('endTime'));
                $token = [
                    'room_start_date' => $startDate->format($GLOBALS['TL_CONFIG']['datimFormat']),
                    'room_end_date' => $endDate->format($GLOBALS['TL_CONFIG']['datimFormat']),
                    'room_repeat' => $repeat > 0 ? true : false,
                    'room_repeat_times' => $repeat,
                    'room_event_title' => Input::post('eventTitle'),
                ];
                $notification = Notification::findByPk($model->room_reservation_notification);
                if (null !== $notification) {
                    $notification->send($token);
                }
            }
            $jumpToPage = PageModel::findPublishedById($this->jumpTo);
            return new RedirectResponse(Environment::get('base') . ltrim($jumpToPage->getFrontendUrl('/month/' . $startDate->format('Ym')), '/'));

        }

        $template->usePricing = $model->room_reservation_use_pricing;
        $template->priceDay = $model->room_reservation_price_day;
        $template->priceHalfDay = $model->room_reservation_price_half_day;
        $template->priceHour = $model->room_reservation_price_hour;
        $template->priceHalfHour = $model->room_reservation_price_half_hour;
        $template->startTime = $model->room_reservation_start_time;
        $template->endTime = $model->room_reservation_end_time;
        $template->minBookingTime = $model->room_reservation_min_booking_time;
        $template->useHalfHour = $model->room_reservation_use_half_hour;
        $template->useHalfDay = $model->room_reservation_use_half_day;
        $template->useEvening = $model->room_reservation_use_evening;
        $template->priceEvening = $model->room_reservation_price_evening;
        $template->eveningStart = $model->room_reservation_evening_start;
        if($model->room_reservation_booking_one_day == '1') {
            //@TODO
            //$this->fields['endDate']->template = 'form_hidden';
        }

        return $template->getResponse();
    }
}

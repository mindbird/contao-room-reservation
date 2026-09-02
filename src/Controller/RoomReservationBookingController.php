<?php

/*
 * This file is part of [mindbird/contao-room-reservation].
 *
 * (c) mindbird
 *
 * @license LGPL-3.0-or-later
 */

namespace Mindbird\Contao\RoomReservation\Controller;

use Contao\CalendarEventsModel;
use Contao\Controller;
use Contao\CoreBundle\Controller\FrontendModule\AbstractFrontendModuleController;
use Contao\CoreBundle\Exception\PageNotFoundException;
use Contao\CoreBundle\Routing\ContentUrlGenerator;
use Contao\CoreBundle\Twig\FragmentTemplate;
use Contao\FrontendUser;
use Contao\Input;
use Contao\ModuleModel;
use Contao\PageModel;
use Contao\Template;
use DateInterval;
use DateTime;
use Mindbird\Contao\RoomReservation\Service\BookingService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Terminal42\NotificationCenterBundle\NotificationCenter;

class RoomReservationBookingController extends AbstractFrontendModuleController
{
    protected $bookingService;
    protected $notificationCenter;
    protected $contentUrlGenerator;

    public function __construct(BookingService $bookingService, NotificationCenter $notificationCenter, ContentUrlGenerator $contentUrlGenerator)
    {
        $this->bookingService = $bookingService;
        $this->notificationCenter = $notificationCenter;
        $this->contentUrlGenerator = $contentUrlGenerator;
    }

    protected function getResponse(FragmentTemplate $template, ModuleModel $model, Request $request): Response
    {
        $GLOBALS['TL_CSS'][] = 'bundles/contaoroomreservation/css/datepicker.min.css|screen|static';
        $GLOBALS['TL_BODY'][] = Template::generateScriptTag(
            Controller::addAssetsUrlTo('bundles/contaoroomreservation/js/datepicker.min.js'),
            false,
            true
        );
        $GLOBALS['TL_BODY'][] = Template::generateScriptTag(
            Controller::addAssetsUrlTo('bundles/contaoroomreservation/js/datepicker-de.js'),
            false,
            true
        );
        $GLOBALS['TL_BODY'][] = Template::generateScriptTag(
            Controller::addAssetsUrlTo('bundles/contaoroomreservation/js/jquery.validate.js'),
            false,
            true
        );

        $template->fields = $this->bookingService->initFields(
            $model->id,
            $model->room_reservation_start_time,
            $model->room_reservation_end_time,
            $model->room_reservation_min_booking_time,
            $model->room_reservation_page_agb
        );

        $user = FrontendUser::getInstance();
        if ('room_reservation_booking_'.$model->id === Input::post('FORM_SUBMIT')) {
            $repeat = 0;
            if (Input::post('repeatTimes') > 0) {
                $repeat = (int) Input::post('repeatTimes');
            }

            $startDate = $this->createDateTime((string) Input::post('startDate'), (string) Input::post('startTime'));
            $endDate = $this->createDateTime((string) Input::post('endDate'), (string) Input::post('endTime'));

            for ($i = 0; $i <= $repeat; ++$i) {
                $addInterval = new DateInterval('P'.(7 * $i).'D');
                $bookingStartDate = clone $startDate;
                $bookingEndDate = clone $endDate;
                $bookingStartDate->add($addInterval);
                $bookingEndDate->add($addInterval);

                if ($this->bookingService->checkAvailability($bookingStartDate, $bookingEndDate, (int) $model->room_event_archive, (int) $model->room_reservation_time_between_entries)) {
                    $cem = new CalendarEventsModel();
                    $cem->pid = $model->room_event_archive;
                    $cem->startDate = $bookingStartDate->format('U');
                    $cem->startTime = $bookingStartDate->format('U');
                    $cem->endDate = $bookingEndDate->format('U');
                    $cem->endTime = $bookingEndDate->format('U');
                    $cem->title = Input::post('eventTitle');
                    $cem->published = true;
                    $cem->addTime = true;
                    $cem->member = null !== $user ? $user->id : null;
                    $cem->save();
                }
            }

            if (0 !== $model->room_reservation_notification) {
                $token = [
                    'room_start_date' => $startDate->format($GLOBALS['TL_CONFIG']['datimFormat']),
                    'room_end_date' => $endDate->format($GLOBALS['TL_CONFIG']['datimFormat']),
                    'room_repeat' => $repeat > 0 ? true : false,
                    'room_repeat_times' => $repeat,
                    'room_event_title' => Input::post('eventTitle'),
                ];

                if (null !== $user && '' !== (string) $user->email) {
                    $token['member_email'] = $user->email;
                }

                $this->notificationCenter->sendNotification((int) $model->room_reservation_notification, $token);
            }
            $jumpToPage = PageModel::findPublishedById($model->room_reservation_jump_to);
            if ($jumpToPage === null) {
                throw new PageNotFoundException('Page #' . $model->room_reservation_jump_to);
            }

            return new RedirectResponse($this->contentUrlGenerator->generate($jumpToPage, ['parameters' => '/month/'.$startDate->format('Ym')]));
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
        $template->roomId = $model->room_event_archive;
        $template->timeBetweenEntries = $model->room_reservation_time_between_entries;
        if ('1' === $model->room_reservation_booking_one_day) {
            //@TODO
            //$this->fields['endDate']->template = 'form_hidden';
        }

        return $template->getResponse();
    }

    private function createDateTime(string $date, string $time): DateTime
    {
        $dateTime = DateTime::createFromFormat('!d.m.Y H:i', $date.' '.$time);
        $errors = DateTime::getLastErrors();

        if (false === $dateTime || (false !== $errors && (0 !== $errors['warning_count'] || 0 !== $errors['error_count']))) {
            throw new BadRequestHttpException('Invalid reservation date or time.');
        }

        return $dateTime;
    }
}

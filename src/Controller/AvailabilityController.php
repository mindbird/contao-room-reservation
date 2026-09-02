<?php


namespace Mindbird\Contao\RoomReservation\Controller;


use Contao\CoreBundle\Controller\AbstractFragmentController;
use Contao\Input;
use Mindbird\Contao\RoomReservation\Service\BookingService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class AvailabilityController extends AbstractFragmentController
{
    /** @var BookingService */
    protected $bookingService;

    public function __construct(BookingService $bookingService)
    {
        $this->bookingService = $bookingService;
    }

    /**
     * @return JsonResponse
     * @throws \Exception
     */
    public function checkAvailability(): JsonResponse
    {
        return new JsonResponse($this->bookingService->checkAvailabilityAjax(
            (int) Input::post('repeat'),
            (string) Input::post('startDate'),
            (string) Input::post('startTime'),
            (string) Input::post('endDate'),
            (string) Input::post('endTime'),
            (int) Input::post('roomId'),
            (int) Input::post('timeBetweenEntries'),
            (int) Input::post('minBookingTime')
        ));

    }
}

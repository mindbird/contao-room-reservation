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
        //@TODO Service und so
        return new JsonResponse($this->bookingService->checkAvailabilityAjax(
            Input::post('repeat'),
            Input::post('startDate'),
            Input::post('startTime'),
            Input::post('endDate'),
            Input::post('endTime'),
            Input::post('roomId')
        ));

    }
}

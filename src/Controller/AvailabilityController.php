<?php


namespace Mindbird\Contao\RoomReservation\Controller;


use Contao\CoreBundle\Controller\AbstractFragmentController;
use Contao\Input;
use Haste\Http\Response\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class AvailabilityController extends AbstractFragmentController
{
    public function checkAvailabilityAction(): Response
    {
        //@TODO Service und so
        return new JsonResponse($this->checkAvailabilityAjax(Input::post('repeat'), Input::post('startDate'), Input::post('startTime'), Input::post('endDate'), Input::post('endTime')));

    }
}

<?php

declare(strict_types=1);

namespace Mindbird\Contao\RoomReservation\NotificationType;

use Terminal42\NotificationCenterBundle\NotificationType\NotificationTypeInterface;
use Terminal42\NotificationCenterBundle\Token\Definition\AnythingTokenDefinition;
use Terminal42\NotificationCenterBundle\Token\Definition\EmailTokenDefinition;
use Terminal42\NotificationCenterBundle\Token\Definition\Factory\TokenDefinitionFactoryInterface;
use Terminal42\NotificationCenterBundle\Token\Definition\TextTokenDefinition;

class RoomReservationNotificationType implements NotificationTypeInterface
{
    public const NAME = 'room_reservation_booking_confirmation';

    public function __construct(private readonly TokenDefinitionFactoryInterface $factory)
    {
    }

    public function getName(): string
    {
        return self::NAME;
    }

    public function getTokenDefinitions(): array
    {
        return [
            $this->factory->create(TextTokenDefinition::class, 'room_start_date', 'room_reservation.room_start_date'),
            $this->factory->create(TextTokenDefinition::class, 'room_end_date', 'room_reservation.room_end_date'),
            $this->factory->create(AnythingTokenDefinition::class, 'room_repeat', 'room_reservation.room_repeat'),
            $this->factory->create(TextTokenDefinition::class, 'room_repeat_times', 'room_reservation.room_repeat_times'),
            $this->factory->create(TextTokenDefinition::class, 'room_event_title', 'room_reservation.room_event_title'),
            $this->factory->create(EmailTokenDefinition::class, 'member_email', 'room_reservation.member_email'),
        ];
    }
}

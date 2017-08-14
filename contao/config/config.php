<?php

/**
 * FRONT END MODULES
 */
array_insert($GLOBALS ['FE_MOD'] ['events'], 1, array(
    'room_reservation' => 'RoomReservation\Module\Booking'
));

$GLOBALS['NOTIFICATION_CENTER']['NOTIFICATION_TYPE']['room_reservation'] = array
(
    // Type
    'room_reservation_booking_confirmation'   => array
    (
        // Field in tl_nc_language
        'recipients'           => array('member_email', 'admin_email'),
        'email_subject'        => array('domain', 'member_*', 'admin_email'),
        'email_text'           => array('domain', 'member_*', 'admin_email', 'room_*'),
        'email_html'           => array('domain', 'member_*', 'admin_email'),
        'file_name'            => array('domain', 'member_*', 'admin_email'),
        'file_content'         => array('domain', 'member_*', 'admin_email'),
        'email_sender_name'    => array('admin_email'),
        'email_sender_address' => array('admin_email'),
        'email_recipient_cc'   => array('admin_email', 'member_*'),
        'email_recipient_bcc'  => array('admin_email', 'member_*'),
        'email_replyTo'        => array('admin_email', 'member_*'),
        'attachment_tokens'    => array('document')
    )
);
<?php

namespace AppBundle\Event;

/**
 * Class NotificationEvent
 * @package AppBundle\Event
 * @author Omar Shaban <omars@php.net>
 */
class NotificationEvent
{
    public $message;
    public $name;

    /**
     * NotificationEvent constructor.
     * @param string $message
     */
    public function __construct($message)
    {
        $this->message = $message;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'event' => [
                'name' => $this->name,
                'message' => $this->message
            ]
        ]
    }
}

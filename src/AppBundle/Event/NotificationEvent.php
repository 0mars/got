<?php
/**
 * Created at 28/03/16 15:46
 */
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

    public function __construct($message)
    {
        $this->message = $message;
    }

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

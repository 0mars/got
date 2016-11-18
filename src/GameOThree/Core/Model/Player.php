<?php
/**
 * Created at 29/03/16 01:49
 */
namespace GameOThree\Core\Model;

/**
 * Class Player
 * @package GameOThree\Core\Model
 * @author Omar Shaban <omars@php.net>
 */
class Player
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var bool
     */
    private $human = false;

    /**
     * Player constructor.
     * @param string $id
     * @param bool $human
     */
    public function __construct($id, $human = false)
    {
        $this->id = $id;
        $this->human = $human;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return bool
     */
    public function isHuman()
    {
        return $this->human;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->id;
    }
}

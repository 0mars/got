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
    private $id;

    private $human = false;

    public function __construct($id, $human = false)
    {
        $this->id = $id;
        $this->human = $human;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return boolean
     */
    public function isHuman()
    {
        return $this->human;
    }

    public function __toString()
    {
        return $this->id;
    }
}

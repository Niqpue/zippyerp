<?php

namespace ZippyERP\ERP;

// вспомагательный   класс  для   вывода  простых  списков
class DataItem implements \Zippy\Interfaces\DataItem
{

    public $id;
    protected $fields = array();

    public final function __set($name, $value)
    {
        $this->fields[$name] = $value;
    }

    public final function __get($name)
    {
        return $this->fields[$name];
    }

    public function getID()
    {
        return $this->id;
    }

}

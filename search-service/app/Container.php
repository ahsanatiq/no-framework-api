<?php
namespace App;

class Container extends \Illuminate\Container\Container
{
    public function isDownForMaintenance()
    {
        return false;
    }
}

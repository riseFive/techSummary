<?php
/**
 * |--------------------------------------------------------------------------
 * |
 * |--------------------------------------------------------------------------
 * Created by PhpStorm.
 * User: weaving
 * Date: 13/6/2018
 * Time: 9:09 AM
 */

namespace Code\DesignPattern\AbstractFactory;


abstract  class AbstractFactory
{
   abstract public function createText(string $content):Text;
}
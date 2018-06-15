<?php
/**
 * |--------------------------------------------------------------------------
 * |
 * |--------------------------------------------------------------------------
 * Created by PhpStorm.
 * User: weaving
 * Date: 13/6/2018
 * Time: 9:12 AM
 */

namespace Code\DesignPattern\AbstractFactory;


abstract class Text
{
    private $text;

    public function __construct( string $text )
    {
        $this->text = $text;
    }
}
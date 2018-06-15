<?php

namespace Code\DesignPattern\DuckTest;

abstract class Duck
{
    protected $flyBehavior;
    protected $quackBehavior;

    public abstract function display();


    public function performFly()
    {
        $this->flyBehavior->fly();
    }

    public function performQuack()
    {
        $this->quackBehavior->quack();
    }
}

interface FlyBehavior
{
    public function fly();
}

class FlyWithWings implements FlyBehavior
{

    public function fly()
    {
        echo "i'm fly";
    }
}

class FlyNoWay implements FlyBehavior
{
    public function fly()
    {
        echo "i'm can't fly";
    }
}

class FlyRocketPowered implements FlyBehavior
{
    public function fly()
    {
        echo "i'm fly with a rocket";
    }
}

interface QuackBehavior
{
    public function quack();
}

class Quack implements QuackBehavior
{
    public function quack()
    {
        echo "quack";
    }
}

class MuteQuack implements QuackBehavior
{
    public function quack()
    {
        echo "<<slience>>";
    }
}

class Squeak implements QuackBehavior
{
    public function quack()
    {
        echo "Squeak";
    }
}

class MallardDuck extends Duck
{


    public function display()
    {
        // TODO: Implement display() method.
    }
}

class ModelDuck extends Duck
{

    public function __construct()
    {
        $flyBehavior   = new FlyNoWay();
        $quackBehavior = new Quack();

        $this->flyBehavior   = $flyBehavior;
        $this->quackBehavior = $quackBehavior;

    }

    public function display()
    {
        echo "i'm model duck";
    }


}

class Client
{
    public function __construct()
    {
        $duck = new ModelDuck();
        $duck->performFly();
        $duck->performQuack();
    }
}
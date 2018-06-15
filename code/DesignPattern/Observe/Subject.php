<?php

namespace Code\DesignPattern\Observe;
interface Subject
{
    public function register( Observer $observer );

    public function remove( Observer $observer );

    public function notify();
}

interface Observer
{
    public function update($template,$humidity,$pressure);
}


interface DisplayElement
{
    public function display();
}

class WeatherData implements Subject
{
    private $observers;
    private $temperature;
    private $humidity;
    private $pressure;


    public function __construct( $temperature, $humidity, $pressure )
    {
        $this->observers   = [];
        $this->temperature = $temperature;
        $this->humidity    = $humidity;
        $this->pressure    = $pressure;
    }

    public function register( Observer $observer )
    {
        $this->observers[] = $observer;
    }

    public function remove( Observer $observer )
    {
        $index = array_search($observer, $this->observers);
        if ($index === false || !array_key_exists($index, $this->observers))
            return false;
        unset($this->observers[ $index ]);
        return true;
    }

    public function notify()
    {
        foreach ( $this->observers as $v ) {
            $v->update();
        }
    }

    public function measurementsChanged()
    {
        $this->notify();
    }


}


class CurrentConditionsDisplay implements Observer,DisplayElement{
    private $temperature;
    private $humidity;
    private $weatherData;

    public function __construct(Subject $weatherData) {
        $this->weatherData=$weatherData;
        $weatherData->register($this);
    }

    public function update($temperature,$humidity,$pressure)
    {
        $this->temperature=$temperature;
        $this->humidity=$humidity;
        $this->display();
    }

    public function display()
    {
       echo '我先大幅度发顺丰'.$this->temperature.'<br/>'.$this->humidity;
    }
}


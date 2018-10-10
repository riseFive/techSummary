<?php

/**
 * |--------------------------------------------------------------------------
 * |
 * |--------------------------------------------------------------------------
 * Created by PhpStorm.
 * User: weaving
 * Date: 2/7/2018
 * Time: 2:23 PM
 */
class Point
{
    public $x;
    public $y;

    public function __construct( $x = 0, $y = 0 )
    {
        $this->x = $x;
        $this->y = $y;
    }

}

class Circle
{
    public $radius;

    public $center;

    const PI = 3.14;

    public function __construct( Point $point, $radius = 1 )
    {
        $this->center = $point;
        $this->radius = $radius;
    }

    public function printCenter(){
        printf('center coordinate is (%d, %d)', $this->center->x, $this->center->y);
    }

    public function area()
    {
        return 3.14 * pow($this->radius, 2);
    }

}

//构建类的对象
function make($className)
{
    $reflectionClass = new ReflectionClass($className);
    $constructor = $reflectionClass->getConstructor();
    $parameters  = $constructor->getParameters();
    $dependencies = getDependencies($parameters);

    return $reflectionClass->newInstanceArgs($dependencies);
}

//依赖解析
function getDependencies($parameters)
{
    $dependencies = [];
    foreach($parameters as $parameter) {
        $dependency = $parameter->getClass();
        if (is_null($dependency)) {
            if($parameter->isDefaultValueAvailable()) {
                $dependencies[] = $parameter->getDefaultValue();
            } else {
                //不是可选参数的为了简单直接赋值为字符串0
                //针对构造方法的必须参数这个情况
                //laravel是通过service provider注册closure到IocContainer,
                //在closure里可以通过return new Class($param1, $param2)来返回类的实例
                //然后在make时回调这个closure即可解析出对象
                //具体细节我会在另一篇文章里面描述
                $dependencies[] = '0';
            }
        } else {
            //递归解析出依赖类的对象
            $dependencies[] = make($parameter->getClass()->name);
        }
    }

    return $dependencies;
}
$circle = make('Circle');
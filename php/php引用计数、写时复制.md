php语法有两种赋值方式：引用赋值、非引用赋值
```php
<?php
$a=10;
$b=$a;
$c=&$b;
```
从表面看，通常会这样认为："引用赋值就是两个变量对应同一个变量(在C中其实就是一个`zval`),非引用赋值则是直接产生的一个新的变量(`zval`)，同时将值`copy`过来”。

但有些情况下则会显得非常低效，例如(#2)：
```php

<?php  
function print_arr($arr){//非引用传递  
    print_r($arr);  
}  
  
$test_arr = array(  
        'a' =>   'a',  
        'b' =>   'b',  
        'c' =>   'c',  
        ...  
    );//这里一个比较大的数组  
  
print_arr($test_arr);//第一次调用print_arr函数执行输出  
print_arr($test_arr);//第二次调用print_arr函数执行输出  
```
如果按照上面的理解方式,那么执行两次`print_arr`，并且是非引用的方式，则会产生两个与`$test_arr`完全相同的新的变量，那么将是非常低效的。
实际代码在运行中，并不会产生两个新的变量。因为PHP内核中已经帮助我们进行了优化。

具体如何实现的呢？这里就要讲到本文的要点：`Reference counting` & `Copy-on-Write`，正是采用引用计数、写时复制这两个机制得以优化。

在介绍这两个机制前，先了解一个基本知识：PHP中的变量在内核中是如何表示的。
PHP中定义的变量都是以一个zval来表示的，zval的定义在Zend/zend.h中定义：
```
typedef struct _zval_struct zval;    
  
typedef union _zvalue_value {  
    long lval;                  /* long value */  
    double dval;                /* double value */  
    struct {  
        char *val;  
        int len;  
    } str;  
    HashTable *ht;              /* hash table value */  
    zend_object_value obj;  
} zvalue_value;  
  
struct _zval_struct {  
    /* Variable information */  
    zvalue_value value;     /* value */  
    zend_uint refcount;  
    zend_uchar type;    /* active type */  
    zend_uchar is_ref;  
};

```
其中，`refcount`和`is_ref`就是实现引用计数、写时复制这两个机制的基础。
`refcount`当前变量存储引用计数，在`zval`初始创建的时候就为1。每增加一个引用，则`refcount ++`。当进行引用分离时，`refcount--`。
`is_ref`用于表示一个`zval`是否是引用状态。`zval`初始化的情况下会是0，表示不是引用。
```php
<?php  
$a;//a:refcount=1,is_ref=0, value=NULL;  
$a = 1; //a:refcount=2,is_ref=0, value=1;  
$b = $a;    //a,b:refcount=3,is_ref=0,value=1;  
$c = $a;    //a,b,c:refcount=4,is_ref=0,value=1;  
$d = &$c; //a,b:refcount=3,is_ref=0,value=1;    c,d:refcount=1, is_ref=1, value=1 
```
上面代码的注释，表示当执行这一行后，refcount与is_ref的变化.

###Copy on Write

>Php变量通过引用计数实现变量共享数据，那如果改变其中一个变量值呢？

当试图写入一个变量时，`Zend`若发现该变量指向的`zval`被多个变量共享，则为其复制一份`ref_count`为1的`zval`，并递减原`zval`的`refcount`，这个过程称为`zval分离`。可见，只有在有写操作发生时zend才进行拷贝操作，因此也叫`copy-on-write(写时拷贝)`

对于引用型变量，其要求和非引用型相反，引用赋值的变量间必须是捆绑的，修改一个变量就修改了所有捆绑变量。
```php
<?php  
    $a=1;  
    $b=$a;  
```
执行过程中的内存结构图：
![](http://orvwtnort.bkt.clouddn.com/201721343/1522828908915.png)


```php
<?php  
    $a=1;  
    $b=&a;  
```
执行过程中的内存结构图：
![](http://orvwtnort.bkt.clouddn.com/201721343/1522828956370.png)
从上可以看到，无论是引用、非引用，这种直接赋值都不会产生新的变量。
只是当是引用时，`is_ref`设置为1。当非引用时，`is_ref`设置为0。
读写复制，就是根据is_ref来进行变量分离的。

当is_ref=1时，是引用变量时，执行“引用下的变量分离”
```php
<?php  
    $a = 1;  
    $b = $a;  
    $c = &$b;  
```
执行过程中的内存结构图：  

![](http://orvwtnort.bkt.clouddn.com/201721343/1522829010513.png)

当is_ref=0时，是非引用变量时，执行“非引用下的变量分离”
```php
<?php  
    $a = 1;  
    $b = &$a;  
    $c = $b;  
```
执行过程中的内存结构图：  

![](http://orvwtnort.bkt.clouddn.com/201721343/1522829055547.png)


只有真正在需要改变变量的值时，
回头在看(#2)代码，可以看到实际上，并没有产生新的变量，始终是$test_arr的变量在输出。所以，这也是为什么很少看到在PHP中使用引用方式传递变量，却仍然不会有性能问题的原因。

文章来源：
https://blog.csdn.net/a600423444/article/details/7030736
<?php
namespace Test;

use Test\Foo as sp42e11a;
class Foo
{
    const ALWAYS_PUBLIC_CONST = 1337;
    public $publicProperty;
    public static $publicStaticProperty;
    protected $protectedProperty;
    protected static $protectedStaticProperty;
    private $sp3318d5;
    private static $sp923e4b;
    public function publicMethod()
    {
        echo $this->publicProperty;
    }
    public final function finalPublicMethod()
    {
    }
    public static function publicStaticMethod()
    {
        echo self::$publicStaticProperty;
        echo sp42e11a::$publicStaticProperty;
    }
    protected function protectedMethod()
    {
        echo $this->protectedProperty;
    }
    protected final function finalProtectedMethod()
    {
    }
    protected static function protectedStaticMethod()
    {
        echo self::$protectedStaticProperty;
        echo sp42e11a::$protectedStaticProperty;
    }
    private function sp1815d4()
    {
        echo $this->sp3318d5;
    }
    private static function sp2754ba()
    {
        echo self::$sp923e4b;
        echo sp42e11a::$sp923e4b;
    }
    private final function sp5b7894()
    {
    }
    public function __construct()
    {
        $this->publicProperty = 100;
        self::$publicStaticProperty = 1;
        sp42e11a::$publicStaticProperty = 2;
        $this->protectedProperty = 200;
        self::$protectedStaticProperty = 1;
        sp42e11a::$protectedStaticProperty = 2;
        $this->sp3318d5 = 300;
        self::$sp923e4b = 1;
        sp42e11a::$sp923e4b = 2;
        $this->publicMethod();
        $this->finalPublicMethod();
        self::publicStaticMethod();
        sp42e11a::publicStaticMethod();
        $this->protectedMethod();
        $this->finalProtectedMethod();
        self::protectedStaticMethod();
        sp42e11a::protectedStaticMethod();
        $this->sp1815d4();
        $this->sp5b7894();
        self::sp2754ba();
        sp42e11a::sp2754ba();
        echo self::ALWAYS_PUBLIC_CONST;
        echo sp42e11a::ALWAYS_PUBLIC_CONST;
    }
}
class Bar extends sp42e11a
{
    public function __construct()
    {
        parent::__construct();
    }
}
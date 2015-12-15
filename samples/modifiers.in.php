<?php

namespace Test;

class Foo
{
    const ALWAYS_PUBLIC_CONST = 1337;

    public $publicProperty;

    public static $publicStaticProperty;

    protected $protectedProperty;

    protected static $protectedStaticProperty;

    private $privateProperty;

    private static $privateStaticProperty;

    public function publicMethod()
    {
        echo $this->publicProperty;
    }

    final public function finalPublicMethod()
    {
        // Final public property does not exist.
    }

    public static function publicStaticMethod()
    {
        echo self::$publicStaticProperty;
        echo Foo::$publicStaticProperty;
    }

    protected function protectedMethod()
    {
        echo $this->protectedProperty;
    }

    final protected function finalProtectedMethod()
    {
        // Final protected property does not exist.
    }

    protected static function protectedStaticMethod()
    {
        echo self::$protectedStaticProperty;
        echo Foo::$protectedStaticProperty;
    }

    private function privateMethod()
    {
        echo $this->privateProperty;
    }

    private static function privateStaticMethod()
    {
        echo self::$privateStaticProperty;
        echo Foo::$privateStaticProperty;
    }

    final private function finalPrivateMethod()
    {
        // Final private property does not exist.
    }

    public function __construct()
    {
        $this->publicProperty = 100;
        self::$publicStaticProperty = 1;
        Foo::$publicStaticProperty = 2;

        $this->protectedProperty = 200;
        self::$protectedStaticProperty = 1;
        Foo::$protectedStaticProperty = 2;

        $this->privateProperty = 300;
        self::$privateStaticProperty = 1;
        Foo::$privateStaticProperty = 2;

        $this->publicMethod();
        $this->finalPublicMethod();
        self::publicStaticMethod();
        Foo::publicStaticMethod();

        $this->protectedMethod();
        $this->finalProtectedMethod();
        self::protectedStaticMethod();
        Foo::protectedStaticMethod();

        $this->privateMethod();
        $this->finalPrivateMethod();
        self::privateStaticMethod();
        Foo::privateStaticMethod();

        echo self::ALWAYS_PUBLIC_CONST;
        echo Foo::ALWAYS_PUBLIC_CONST;
    }
}

class Bar extends Foo
{
    public function __construct()
    {
        // The parent keyword is a special one.
        parent::__construct();
    }
}

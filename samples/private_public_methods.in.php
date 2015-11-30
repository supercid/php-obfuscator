<?php

namespace Test;

class Foo
{
    public static function invoke(Foo $instance)
    {
        // Same class, other instance, direct call.
        $instance->call();

        // Same class, other instance, indirect call.
        $foo = new Foo();
        $foo->call();

        // Same class, other instance, indirect call.
        $x = new Foo();
        $y = $x;
        $z = $y;
        $z->call();

        // Same class, other instance, direct call.
        (new Foo())->call();

        // Other class, other instance, indirect call.
        $bar = new Bar();
        $bar->call();

        // Other class, other instance, direct call.
        (new Bar())->call();
    }

    private function call()
    {
        echo "Foo called!\n";
    }
}

class Bar
{
    public function call()
    {
        echo "Bar called!\n";
    }
}

Foo::invoke(new Foo());

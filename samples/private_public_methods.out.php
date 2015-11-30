<?php
namespace Test;

class Foo
{
    public static function invoke(Foo $spbe7fc4)
    {
        $spbe7fc4->sp54b38c();
        $sp5745e1 = new Foo();
        $sp5745e1->sp54b38c();
        $sp550049 = new Foo();
        $spa7e26d = $sp550049;
        $spf100fe = $spa7e26d;
        $spf100fe->sp54b38c();
        (new Foo())->sp54b38c();
        $spa8ed7d = new Bar();
        $spa8ed7d->call();
        (new Bar())->call();
    }
    private function sp54b38c()
    {
        echo 'Foo called!
';
    }
}
class Bar
{
    public function call()
    {
        echo 'Bar called!
';
    }
}
Foo::invoke(new Foo());

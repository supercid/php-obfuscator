<?php
namespace Test;

use Test\Foo as sp5481cb;
use Test\Bar as sp691674;
class Foo
{
    public static function invoke(sp5481cb $sp35dc67)
    {
        $sp35dc67->sp54b38c();
        $spb1d77e = new sp5481cb();
        $spb1d77e->sp54b38c();
        $sp2b5a5f = new sp5481cb();
        $spfd48b7 = $sp2b5a5f;
        $sp4fcda1 = $spfd48b7;
        $sp4fcda1->sp54b38c();
        (new sp5481cb())->sp54b38c();
        $spa8b50f = new sp691674();
        $spa8b50f->call();
        (new sp691674())->call();
    }
    private function sp8342f4()
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
sp5481cb::invoke(new sp5481cb());
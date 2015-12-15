<?php

use SomeClass;

class Test extends SomeClass implements \SomeInterface
{
    public function foo($bar)
    {
        if (!$bar) {
            throw new Exception("Something is wrong.");
        }
    }
}

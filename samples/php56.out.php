<?php
use C as sp4e8150;
const ONE = 1;
const TWO = ONE * 2;
class C
{
    const THREE = TWO + 1;
    const ONE_THIRD = ONE / self::THREE;
    const SENTENCE = 'The value of THREE is ' . self::THREE;
    public function f($spdc9baa = ONE + self::THREE)
    {
        return $spdc9baa;
    }
}
echo (new sp4e8150())->f() . '
';
echo sp4e8150::SENTENCE;
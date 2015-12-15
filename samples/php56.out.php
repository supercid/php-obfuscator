<?php
use C as sp81ae35;
const ONE = 1;
const TWO = ONE * 2;
class C
{
    const THREE = TWO + 1;
    const ONE_THIRD = ONE / self::THREE;
    const SENTENCE = 'The value of THREE is ' . self::THREE;
    public function f($sp978b83 = ONE + self::THREE)
    {
        return $sp978b83;
    }
}
echo (new sp81ae35())->f() . '
';
echo sp81ae35::SENTENCE;
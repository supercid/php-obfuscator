<?php
const ONE = 1;const TWO = ONE * 2;class C{const THREE = TWO + 1;const ONE_THIRD = ONE / self::THREE;const SENTENCE = 'The value of THREE is ' . self::THREE;public function f($sp19cad7 = ONE + self::THREE){return $sp19cad7;}}echo (new C())->f() . '
';echo C::SENTENCE;

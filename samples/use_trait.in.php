<?php

namespace Test;

use \MyTrait as MyRelativeTrait;
use MyOtherTrait;

class MyClass
{
    use \MyAbsoluteTrait;
    use MyRelativeTrait;
    use MyOtherTrait;
}

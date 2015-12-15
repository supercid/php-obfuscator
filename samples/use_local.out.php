<?php
namespace Test;

use Test\FooException as speaae97;
use Exception as spe77b86;
class FooException extends spe77b86
{
    const ERROR_CODE = 1;
}
throw new speaae97(speaae97::ERROR_CODE);
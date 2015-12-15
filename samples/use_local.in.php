<?php

namespace Test;

use \Exception as TheException;

class FooException extends TheException
{
    const ERROR_CODE = 1;
}

throw new FooException(FooException::ERROR_CODE);

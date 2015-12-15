<?php

namespace A
{
    // Define TheException as \Exception.
    use \Exception as TheException;

    function foo()
    {
        throw new TheException("Exception.");
    }

    \A\foo();
}

namespace B
{
    // Re-define TheException as \InvalidArgumentException.
    use \InvalidArgumentException as TheException;

    function foo()
    {
        throw new TheException("Invalid argument exception.");
    }

    \B\foo();
}

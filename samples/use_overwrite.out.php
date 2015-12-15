<?php
namespace A;

use Exception as spe77b86;
function foo()
{
    throw new spe77b86('Exception.');
}
\A\foo();
namespace B;

use InvalidArgumentException as sp75fb96;
function foo()
{
    throw new sp75fb96('Invalid argument exception.');
}
\B\foo();
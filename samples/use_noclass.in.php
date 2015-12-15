<?php

use \Exception as PhpException;

function foo()
{
    throw new PhpException("Hello!");
}

use \SplTempFileObject;
$file = new SplTempFileObject();

if (!$file->valid() == 0) {
    foo();
}

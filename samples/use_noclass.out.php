<?php
use Exception as sp93553c;
function foo()
{
    throw new sp93553c('Hello!');
}
use SplTempFileObject as sp176274;
$spc8ed6d = new sp176274();
if (!$spc8ed6d->valid() == 0) {
    foo();
}
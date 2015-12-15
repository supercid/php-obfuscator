<?php
use Phalcon\Mvc\Model as spd22445;
use Phalcon\Validation\Validator\Email as sp472927;
use Phalcon\Validation\Validator\Uniqueness as sp85cc3f;
/** @Table( source="users" ) */
abstract class User extends spd22445
{
    /** @IntegerField @Identity @Primary @GetSet */
    protected $id;
    /** @var string The email address of the current user.  @StringField(length=255, nullable=false) @GetSet */
    protected $emailAddress;
    /** @var string The user name of the current user.  @StringField(length=64, nullable=false) @GetSet */
    protected $username;
    /** @var string The name of the current user.  @StringField(length=64, nullable=false) @GetSet */
    protected $name;
    /** @var string The user password.  @StringField(length=128, nullable=false) @GetSet */
    protected $password;
    /** @var string Role of the user.  @EnumField(choices=[ "Guest", "Superuser", "Administrator", "User" ], nullable=false) @GetSet @ToArray("public") */
    protected $role;
    /** @Validator */
    public function validateEmailAddress($sp6a587d)
    {
        $sp6a587d->add('emailAddress', new sp472927(array('model' => $this)));
    }
}
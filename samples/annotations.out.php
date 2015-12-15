<?php
use Phalcon\Mvc\Model as spe4eb26;
use Phalcon\Validation\Validator\Email as sp4ff688;
use Phalcon\Validation\Validator\Uniqueness as spf2f00a;
/**  * @Table(  *   source="users"  * ) */
abstract class User extends spe4eb26
{
    /** * @IntegerField      * @Identity      * @Primary      * @GetSet */
    protected $id;
    /** * @var string The email address of the current user.      *      * @StringField(length=255, nullable=false)      * @GetSet */
    protected $emailAddress;
    /** * @var string The user name of the current user.      *      * @StringField(length=64, nullable=false)      * @GetSet */
    protected $username;
    /** * @var string The name of the current user.      *      * @StringField(length=64, nullable=false)      * @GetSet */
    protected $name;
    /** * @var string The user password.      *      * @StringField(length=128, nullable=false)      * @GetSet */
    protected $password;
    /** * @Validator */
    public function validateEmailAddress($spf881d9)
    {
        $spf881d9->add('emailAddress', new sp4ff688(array('model' => $this)));
    }
}
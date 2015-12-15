<?php
use Phalcon\Mvc\Model as sp8dc92b;
use Phalcon\Validation\Validator\Email as sp3af4f6;
use Phalcon\Validation\Validator\Uniqueness as sp2848c9;
/** * @Table(  *   source="users"  * ) */
abstract class User extends sp8dc92b
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
    public function validateEmailAddress($spe54f33)
    {
        $spe54f33->add('emailAddress', new sp3af4f6(array('model' => $this)));
    }
}
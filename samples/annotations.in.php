<?php

use Phalcon\Mvc\Model;
use Phalcon\Validation\Validator\Email as EmailValidator;
use Phalcon\Validation\Validator\Uniqueness as UniquenessValidator;

/**
 * Base user model.
 *
 * @Table(
 *   source="users"
 * )
 */
abstract class User extends Model
{
    /**
     * @IntegerField
     * @Identity
     * @Primary
     * @GetSet
     */
    protected $id;

    /**
     * @var string The email address of the current user.
     *
     * @StringField(length=255, nullable=false)
     * @GetSet
     */
    protected $emailAddress;

    /**
     * @var string The user name of the current user.
     *
     * @StringField(length=64, nullable=false)
     * @GetSet
     */
    protected $username;

    /**
     * @var string The name of the current user.
     *
     * @StringField(length=64, nullable=false)
     * @GetSet
     */
    protected $name;

    /**
     * @var string The user password.
     *
     * @StringField(length=128, nullable=false)
     * @GetSet
     */
    protected $password;

    /**
     * @var string Role of the user.
     *
     * @EnumField(choices=[
     *     "Guest", "Superuser", "Administrator", "User"
     * ], nullable=false)
     * @GetSet
     * @ToArray("public")
     */
    protected $role;

    /**
     * @Validator
     */
    public function validateEmailAddress($validator)
    {
        $validator->add(
            "emailAddress",
            new EmailValidator([
                "model" => $this
            ])
        );
    }
}

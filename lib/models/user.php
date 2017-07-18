<?php
namespace models;

use core;
use \Exception;

/**
 * @author Jason Wright <jason@silvermast.io>
 * @since 1/4/17
 * @package prunejuice
 */
class User extends core\Model {

    const TABLE = 'users';

    const PERMLEVELS = [
        'Owner'  => 1,
        'Admin'  => 10,
        'Member' => 20,
    ];

    public $id;
    public $accountId;
    public $name;
    public $email;
    public $permLevel = 20;
    public $passhash;
    public $contentKeyEncrypted;

    /** @var User */
    private static $_me = null;

    /**
     * @return $this
     * @throws Exception
     */
    public function validate() {
        if (mb_strlen($this->id) > 1024) throw new Exception('Invalid id', 400);
        if (mb_strlen($this->name) > 1024) throw new Exception('Invalid name', 400);
        if (mb_strlen($this->email) > 1024) throw new Exception('Invalid email', 400);
        if (empty($this->accountId)) throw new Exception('Invalid Account', 400);

        // sha256 = 64 bytes
        if (mb_strlen($this->passhash) != 64) throw new Exception('Invalid password hash', 400);
        if (!isset($this->contentKeyEncrypted->cipher)) throw new Exception('Invalid Content Key', 400);

        return $this;
    }

    /**
     * Returns the authenticated user
     * @return User|null
     */
    public static function me() {
        if (!isset($_SESSION['user_id']))
            return null;

        if (!self::$_me instanceof self)
            self::$_me = self::findOne(['id' => $_SESSION['user_id']]);

        return self::$_me;
    }

    /**
     * @param $password
     * @return mixed
     */
    public static function hashPassword($password) {
        return hash_pbkdf2('sha256', $password, 'Charon.UserKeychain.PassHash', 20);
    }

    /**
     * Hashes the password and sets it into the object
     * @param string $password plaintext
     * @return self
     * @throws Exception
     */
    public function setPassword($password) {
        if (mb_strlen($password) < 12) throw new Exception('Passwords must be at least 12 characters. Type whatever you\'d like, though!');
        $this->passhash = self::hashPassword($password);
        return $this;
    }

}
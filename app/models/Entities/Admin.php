<?php
namespace Models\Entities;

use Laminas\Crypt\Password\Bcrypt;
use Laminas\Math\Rand;
use \Models\Entities\Generated;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Table;

/**
 * @Entity(repositoryClass="\Models\Repositories\Admin")
 * @Table(name="tbl_admin")
 */
class Admin extends Generated\Admin
{
    const GROUP_SUPPORT = 'SUPPORT';
    const GROUP_SUPPER_ADMIN = 'SUPPER_ADMIN';
    const GROUP_MANAGER = 'MANAGER';
    const GROUP_STAFF = 'STAFF';
    const GROUP_TESTER = 'TESTER';

    const GENDER_MALE = 'MALE';
    const GENDER_FEMALE = 'FEMALE';
    const GENDER_OTHER = 'OTHER';

    const LENGTH_OF_KEY = 2;
    const PASSWORD_RAND_LENGTH = 5;
    const PASSWORD_COST = 10;

    const PASSWORD_RAND_CHAR = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

    const IMG_FOLDER = 'images';

    const USER_FOLDER_TOKEN = 'reset_pw';

    const PROFILE_FOLDER_TOKEN = 'upload_avatar';
    /**
     * Enscrypt password
     *
     * @param string $str
     * @return string
     */
    public function encryptPass(string $str = ''): string
    {
        $bcrypt = new Bcrypt(['cost' => self::PASSWORD_COST]);
        $pass = strrev(
            str_replace(self::getPassCost(), '', $bcrypt->create($str))
        );

        $pass = Rand::getString(self::PASSWORD_RAND_LENGTH, self::PASSWORD_RAND_CHAR, true)
            . $pass .
            Rand::getString(self::PASSWORD_RAND_LENGTH, self::PASSWORD_RAND_CHAR, true);

        unset($bcrypt);
        $length = strlen($pass);
        $splitLength = rand(20, 40);
        $lengthKey = Rand::getString(
            self::LENGTH_OF_KEY,
            'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
            true
        );

        $pass1 = substr($pass, 0, $splitLength);
        $pass2 = substr($pass, $splitLength);

        $index = $length - $splitLength;
        return "{$index}{$lengthKey}" . $pass2 . $pass1;
    }
    /**
     * Sets password
     *
     * @param string $password
     * @return void
     */
    public function setAdm_password(string $password = ''): void
    {
        $this->adm_password = self::encryptPass($password);
    }

    /**
     * Returns the admin password.
     *
     * @param bool $decode Whether or not to decode the password.
     *
     * @return string The admin password.
     */
    public function getAdm_password(bool $decode = false): string
    {
        if ($decode === true) {
            $index = (int) $this->adm_password;

            $password = substr(
                $this->adm_password,
                strlen((string) $index) + self::LENGTH_OF_KEY
            );
            $password2 = substr($password, 0, $index);
            $password1 = substr($password, $index);

            $password = substr(
                $password1 . $password2,
                self::PASSWORD_RAND_LENGTH,
                strlen($password) - 2 * self::PASSWORD_RAND_LENGTH
            );
            return self::getPassCost() . strrev($password);
        }
        return $this->adm_password;
    }

    /**
     * Returns the password cost string.
     *
     * @return string The password cost string.
     */
    public function getPassCost(): string
    {
        return '$2y$' . sprintf('%1$02d', self::PASSWORD_COST) . '$';
    }

    /**
     * Return json name
     *
     * @return array
     */
    public function getAdm_json_name(): array
    {
        if ($this->adm_json_name) {
            return @json_decode($this->adm_json_name, true);
        }
        return[
            'first' => '',
            'last' => '',
        ];
    }

    /**
     * Set json name
     *
     * @param array $data
     * @return self
     */
    public function setAdm_json_name(array $data = []): self
    {
        $json = empty($data) 
            ? '{"first":"","last":""}'
            : @json_encode($data);
        $this->adm_json_name = $json;

        return $this;
    }
    
    /**
     * Get user upload folder
     *
     * @param string $admId
     * @return string
     */
    public static function getUploadFolder(string $admId = ''): string
    {
        return ROOT_UPLOAD_PATH . '/'. APPLICATION_SITE. "/{$admId}";
    }

    /**
     * get image path
     *
     * @param string $id
     * @param string $img
     * @return string
     */
    public static function getImgPath(string $id = '__id__', string $img = ''): string
    {
        return '/uploads/'. APPLICATION_SITE ."/{$id}/" . self::IMG_FOLDER . "/{$img}";
    }

    /**
     * Group permission
     *
     * @return array
     */
    public static function returnGroupCodes(): array
    {
        return [
            self::GROUP_STAFF => self::GROUP_STAFF,
            self::GROUP_MANAGER => self::GROUP_MANAGER,
            self::GROUP_SUPPER_ADMIN => self::GROUP_SUPPER_ADMIN,
            self::GROUP_SUPPORT => self::GROUP_SUPPORT,
            self::GROUP_TESTER => self::GROUP_TESTER,
        ];
    }

    /**
     * Group permission for manager
     *
     * @return array
     */
    public static function returnGroupCodesForManager(): array
    {
        return [
            self::GROUP_MANAGER => self::GROUP_MANAGER,
            self::GROUP_SUPPER_ADMIN => self::GROUP_SUPPER_ADMIN,
            self::GROUP_SUPPORT => self::GROUP_SUPPORT,
            self::GROUP_TESTER => self::GROUP_TESTER,
        ];
    }

    /**
     * Get code to search admin account
     * @param string $code
     * @return array
     */
    public static function returnSearchGroupCodesByCode(string $code = ''): array
    {
        if (empty($code)) return [];
        $groups = array_keys(self::returnGroupCodes());
        $idx = $code == self::GROUP_SUPPORT
        ? 100
        : array_search($code, $groups);
        
        $codes = array_splice(
            $groups, 0, $idx + 1
        );
        return array_combine($codes, $codes);;
    }

    /**
     * @param bool $timestamp
     * @return mixed
     */
    public function getAdm_last_access_time(bool $timestamp = true): mixed
    {
        if (false === $timestamp)
            return $this->adm_last_access_time ?
            date(APPLICATION_DATE_TIME, $this->adm_last_access_time)
            : '';
        return $this->adm_last_access_time;
    }
}
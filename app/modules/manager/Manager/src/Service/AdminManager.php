<?php

namespace Manager\Service;
use Laminas\Crypt\Password\Bcrypt;
use Models\Entities\Admin;

/**
 * This service is responsible for adding/editing users
 * and changing user password.
 */
class AdminManager
{
    /**
     * Doctrine entity manager.
     * @var Doctrine\ORM\EntityManager
     */
    private $entityManager;

    /**
     * Constructs the service.
     */
    public function __construct($entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Checks that the given password is correct
     *
     * @param Admin $user
     * @param string $password
     * @return boolean
     */
    public function validPassword(Admin $user, string $password): bool
    {
        $bcrypt = new Bcrypt();
        $passwordHash = $user->getAdm_password(true);

        if ($bcrypt->verify($password, $passwordHash)) {
            return true;
        }

        return false;
    }

    /**
     * This method is used to change the password for the given user. To change the password,
     * one must know the old password.
     *
     * @param Admin $user
     * @param array $data
     * @return boolean
     */
    public function changePassword(Admin $user, array $data): bool
    {
        $oldPass = $data['current_pass'] ?? '';

        // Check that old password is correct
        if (!$this->validPassword($user, $oldPass)) {
            return false;
        }       
       
        $newPass = $data['new_pass'] ?? '';
        // Check password length
        if (strlen($newPass) < 6 || strlen($newPass) > 50) {
            return false;
        }

        $newPassConfirm = $data['confirm_new_pass'] ?? '';
        // Check match password
        if ($newPassConfirm !== $newPass) {
            return false;
        }

        $user->adm_password = $newPass;
        $this->entityManager->flush($user);

        return true;
    }

    /**
     * hecks whether an active user with given email address already exists in the database. 
     *
     * @param string $email
     * @return boolean
     */
    public function checkUserExists(string $email): bool
    {
        $user = $this->entityManager->getRepository(Admin::class)
        ->findOneBy([ 'adm_email' => $email ]);
        
        return $user !== null;
    }
}

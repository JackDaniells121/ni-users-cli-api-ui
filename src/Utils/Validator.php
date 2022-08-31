<?php

namespace App\Utils;

use App\Repository\UserRepository;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Exception\RuntimeException;
use function Symfony\Component\String\u;

class Validator
{
    public function __construct(
        private UserRepository $users,
    )
    {}

    public function validateName(?string $username): string
    {
        if (empty($username)) {
            throw new InvalidArgumentException('The username can not be empty.');
        }

        if (1 !== preg_match("/^[a-zA-ZàáâäãåąčćęèéêëėįìíîïłńòóôöõøùúûüųūÿýżźñçčšśžÀÁÂÄÃÅĄĆČĖĘÈÉÊËÌÍÎÏĮŁŃÒÓÔÖÕØÙÚÛÜŲŪŸÝŻŹÑßÇŒÆČŠŽ∂ð'-]+$/", $username)) {
            throw new InvalidArgumentException('Name must contain only letters.');
        }

        return $username;
    }

    public function validateEmail(?string $email): string
    {
        if (empty($email)) {
            throw new InvalidArgumentException('The email can not be empty.');
        }

        if (null === u($email)->indexOf('@')) {
            throw new InvalidArgumentException('The email should look like a real email.');
        }

        $existingUser = $this->users->findOneBy(['email' => $email]);

        if (null !== $existingUser) {
            throw new RuntimeException(sprintf('There is already a user registered with the "%s" email.', $email));
        }

        return $email;
    }

     public function validatePesel(string $pesel): string
     {
         if (1 !== preg_match('/^[0-9]{11}$/', $pesel)) {
             throw new InvalidArgumentException('Pesel must consists only digits and should be exactly 11 chars long.');
         }
         // check if a user with the same username already exists.
         $existingUser = $this->users->findOneBy(['pesel' => $pesel]);

         if (null !== $existingUser) {
             throw new RuntimeException(sprintf('There is already a user registered with the "%s" pesel.', $pesel));
         }

         if (true !== Pesel::validateCheckSum($pesel)) {
             throw new RuntimeException(sprintf('This pesel seems not valid (incorrect checksum): "%s" pesel.', $pesel));
         }

         return $pesel;
     }
}
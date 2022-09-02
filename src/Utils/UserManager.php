<?php

namespace App\Utils;

use App\Entity\Skill;
use App\Entity\User;
use App\Entity\UserSkill;
use App\Repository\SkillRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Exception\RuntimeException;

class UserManager
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserRepository $users,
        private SkillRepository $skills,
        private Pesel $peselHelper,
        private Validator $validator,
    )
    {
    }

    public function saveData($name, $surname, $email, $pesel, $skills, $source)
    {
        $user = new User();
        $user->setSource($source);
        $user->setName($name);
        $user->setSurname($surname);
        $user->setEmail($email);
        $user->setPesel($pesel);
        $user->setActivated(false);

        foreach (explode(',', $skills) as $skill) {
            $existingSkill = $this->skills->findOneByName(trim($skill));
            $userSkill = null;
            if ($existingSkill) {
                $userSkill = new UserSkill();
                $userSkill->setSkillId($existingSkill);
            }
            else {
                $newSkill = new Skill();
                $newSkill->setName(trim($skill));
                $this->entityManager->persist($newSkill);
                $userSkill = new UserSkill();
                $userSkill->setSkillId($newSkill);
            }
            $this->entityManager->persist($userSkill);
            $user->addUserSkill($userSkill);
        }

        $this->saveUser($user);
        return $user;
    }

    public function validateData($name, $surname, $email, $pesel, $skills): void
    {
        $this->validator->validatePesel($pesel);
        $this->validator->validateName($name);
        $this->validator->validateName($surname);
        $this->validator->validateEmail($email);
    }

    public function activateUser(User $user)
    {
        $user->setActivated(true);
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    public function saveUser(User $user)
    {
        $this->validateData(
            $user->getName(),
            $user->getSurname(),
            $user->getEmail(),
            $user->getPesel(),
            $user->getUserSkills()
        );

        if (Pesel::getAge($user->getPesel()) >= 18) {
            $user->setActivated(true);
        }

        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
}
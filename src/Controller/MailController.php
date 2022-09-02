<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MailController extends AbstractController
{

    public static function sendUserActivationEmail( User $user): bool
    {
        $result = mail(
            $user->getEmail(),
            'Account Activation Success!',
            '<p>Your account has been activated.!</p>'
        );
        return $result;
    }
}

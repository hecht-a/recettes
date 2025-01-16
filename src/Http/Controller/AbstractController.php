<?php

namespace App\Http\Controller;

use App\Domain\Auth\User;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @method User|null getUser()
 */
class AbstractController extends \Symfony\Bundle\FrameworkBundle\Controller\AbstractController
{
    public function __construct(protected readonly EntityManagerInterface $em)
    {
    }

    protected function getUserOrThrow(): User
    {
        $user = $this->getUser();
        if (!($user instanceof User)) {
            throw $this->createAccessDeniedException();
        }

        return $user;
    }
}

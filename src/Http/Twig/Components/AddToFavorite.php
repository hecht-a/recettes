<?php

namespace App\Http\Twig\Components;

use App\Domain\Auth\User;
use App\Domain\Recipe\Recipe;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
final class AddToFavorite
{
    use DefaultActionTrait;

    #[LiveProp]
    public Recipe $recipe;

    #[LiveProp]
    public bool $isLiked;

    public function __construct(private readonly Security $security, private readonly EntityManagerInterface $entityManager)
    {
    }

    #[LiveAction]
    #[IsGranted('ROLE_USER')]
    public function toggleLike(): void
    {
        /** @var User $user */
        $user = $this->security->getUser();
        $recipe = $this->recipe;

        if ($this->isLiked) {
            $recipe->removeUserFavorite($user);
            $this->isLiked = false;
        } else {
            $recipe->addUserFavorite($user);
            $this->isLiked = true;
        }

        $this->entityManager->flush();
    }
}

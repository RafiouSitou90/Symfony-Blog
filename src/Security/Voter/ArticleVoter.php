<?php

namespace App\Security\Voter;

use App\Entity\Articles;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class ArticleVoter extends Voter
{
    public const DELETE = 'delete';
    public const EDIT = 'edit';
    public const SHOW = 'show';

    protected function supports($attribute, $subject)
    {
        return in_array($attribute, [self::DELETE, self::EDIT, self::SHOW])
            && $subject instanceof Articles;
    }

    /**
     * {@inheritdoc}
     *
     * @param string $attribute
     * @param Articles $article
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute($attribute, $article, TokenInterface $token)
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        return $user === $article->getAuthor();
    }
}

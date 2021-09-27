<?php

namespace App\Security\Voter;

use App\Entity\Task;
use LogicException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class TaskVoter extends Voter
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, $subject): bool
    {
        return 'MANAGE_TASK' == $attribute
            && $subject instanceof Task;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        $task = $subject;

        if (!$user instanceof UserInterface) {
            return false;
        }

        switch ($attribute) {
            case 'MANAGE_TASK':
                if (['ROLE_ADMIN'] === $this->security->getUser()->getRoles() && null === $task->getAuthor()) {
                    return true;
                }

                return $user === $task->getAuthor();
        }
        // @codeCoverageIgnoreStart
        throw new LogicException('This code should not be reached!');
        // @codeCoverageIgnoreEnd
    }
}

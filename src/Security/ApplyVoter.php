<?php

namespace App\Security;

use App\Entity\Apply;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ApplyVoter extends Voter
{

    const VIEW = 'view_apply';
    const VIEW_CV = 'view_apply_cv';


    protected function supports($attribute, $subject)
    {
        if (!in_array($attribute, [self::VIEW, self::VIEW_CV])) {
            return false;
        }

        if (!$subject instanceof Apply) {
            return false;
        }

        return true;
    }

    private $decisionManager;

    public function __construct(AccessDecisionManagerInterface $decisionManager)
    {
        $this->decisionManager = $decisionManager;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            // the user must be logged in; if not, deny access
            return false;
        }

        // you know $profile is a User object, thanks to supports
        /** @var Apply $apply */
        $apply = $subject;


        switch ($attribute) {
            case self::VIEW:
                return $this->canView($apply, $user);
                break;
            case self::VIEW_CV:
                return $this->canViewCv($apply, $user);
                break;
        }

        throw new \LogicException('This code should not be reached!');
    }

    private function canView(Apply $apply, User $user)
    {
        return $apply->getProject()->getUser() === $user || $apply->getWorker()->getUser() === $user;
    }

    private function canViewCv(Apply $apply, User $user)
    {
        return $apply->getProject()->getUser() === $user;
    }

}

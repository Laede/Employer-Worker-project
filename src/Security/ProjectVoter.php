<?php

namespace App\Security;

use App\Entity\Project;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ProjectVoter extends Voter
{

    const VIEW = 'view';
    const EDIT = 'edit';
    const DELETE = 'delete';


    protected function supports($attribute, $subject)
    {

        if (!in_array($attribute, array(self::VIEW, self::EDIT, self::DELETE))) {
            return false;
        }

        if (!$subject instanceof Project) {
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

        if ($this->decisionManager->decide($token, array('ROLE_ADMIN'))) {
            return true;
        }


        if (!$user instanceof User) {
            // the user must be logged in; if not, deny access
            return false;
        }

        // you know $profile is a User object, thanks to supports
        /** @var User $project */
        $project = $subject;


        switch ($attribute) {
            case self::VIEW:
                return $this->canView($project, $user);
            case self::EDIT:
                return $this->canEdit($project, $user);
            case self::DELETE:
                return $this->canDelete($project, $user);

        }

        throw new \LogicException('This code should not be reached!');
    }


    private function canView(User $project, User $user)
    {
        // if they can edit, they can view
        if ($this->canEdit($project, $user)) {
            return true;
        }
        return;
    }

    private function canEdit(User $project, User $user)
    {
        // this assumes that the data object has a getOwner() method
        // to get the entity of the user who owns this data object
        return $user->getUsername() === $project->getUsername();
    }

    private function canDelete(User $project, User $user)
    {

        return $user->getUsername() === $project->getUsername();
    }


}

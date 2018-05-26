<?php

namespace App\Security;

use App\Entity\Project;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ProjectVoter extends Voter
{

    const IS_AUTHOR = 'is_project_author';


    protected function supports($attribute, $subject)
    {

        if (!in_array($attribute, array(self::IS_AUTHOR))) {
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

        if (!$user instanceof User) {
            return false;
        }

        /** @var Project $project */
        $project = $subject;


        switch ($attribute) {
            case self::IS_AUTHOR:
                return $this->isAuthor($project, $user);

        }

        throw new \LogicException('This code should not be reached!');
    }

    private function isAuthor(Project $project, User $user)
    {
        return $project->getUser() === $user;
    }


}

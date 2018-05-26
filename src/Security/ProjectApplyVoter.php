<?php

namespace App\Security;

use App\Entity\Project;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ProjectApplyVoter extends Voter
{

    const APPLY = 'worker_apply';
    const VIEW = 'worker_view_project';
    const NOT_EXPIRED = 'project_not_expired';
    const NOT_FULL = 'project_not_full';
    const NOT_APPLIED = 'project_not_applied_yet';


    protected function supports($attribute, $subject)
    {
        if (!in_array($attribute, [self::APPLY, self::VIEW, self::NOT_EXPIRED, self::NOT_FULL, self::NOT_APPLIED])) {
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
            // the user must be logged in; if not, deny access
            return false;
        }

        // you know $profile is a User object, thanks to supports
        /** @var Project $project */
        $project = $subject;


        switch ($attribute) {
            case self::APPLY:
                return $this->canApply($project, $user);
                break;
            case self::VIEW:
                return $this->canView($project, $user);
                break;
            case self::NOT_EXPIRED:
                return $this->notExpired($project);
                break;
            case self::NOT_FULL:
                return $this->notFull($project);
                break;
            case self::NOT_APPLIED:
                return $this->notAppliedYet($project, $user);
                break;
        }

        throw new \LogicException('This code should not be reached!');
    }

    private function canApply(Project $project, User $user)
    {
        return $this->notExpired($project)
            && $this->notFull($project)
            && $this->notAppliedYet($project, $user);
    }

    private function canView(Project $project, $user)
    {
        return !$this->notAppliedYet($project, $user)
         || ($this->notExpired($project) && $this->notFull($project));
    }

    private function notAppliedYet(Project $project, User $user)
    {
        foreach($project->getApplies() as $apply) {
            if($apply->getWorker()->getUser() === $user) {
                return false;
            }
        }
        return true;
    }

    private function notExpired(Project $project)
    {
        return $project->getRegisterDeadline() >= date('Y-m-d');
    }

    private function notFull(Project $project)
    {
        $count = 0;
        foreach($project->getApplies() as $apply) {
            if ($apply->getStatus() === 'accepted') {
                $count++;
            }
        }
        return $count < $project->getCrewCount();
    }

}

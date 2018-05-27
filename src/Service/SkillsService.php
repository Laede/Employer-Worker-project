<?php

namespace App\Service;


use App\Entity\Project;
use App\Entity\Skills;
use App\Entity\Worker;
use App\Repository\SkillsRepository;
use Doctrine\Common\Persistence\ObjectManager;

class SkillsService
{
    private $skillsRepository;

    public function __construct(SkillsRepository $skillsRepository)
    {
        $this->skillsRepository = $skillsRepository;
    }

    public function proceedSkills($object, ObjectManager $em)
    {
        if($object instanceof Worker || $object instanceof Project) {
            $skills = explode(',',strtolower($object->getSkillsString(null)));

            $ids = [];
            $available = $this->skillsRepository->findByNames($skills);
            /** @var Skills $skill */
            foreach($available as $skill) {
                if (($key = array_search(strtolower($skill->getName()), $skills)) !== false) {
                    $ids[] = $skill->getId();
                    unset($skills[$key]);
                }
            }

            foreach($skills as $skill) {
                $new_skill = new Skills();
                $new_skill->setName($skill);
                $em->persist($new_skill);
                $em->flush();
                $ids[] = $new_skill->getId();
            }

            $object->clearSkills();
            $em->flush();
            foreach($ids as $id) {
                $object->addSkill($this->skillsRepository->find($id));
            }
        }


        return $object;
    }
}
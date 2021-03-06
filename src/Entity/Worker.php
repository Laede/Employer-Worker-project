<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\WorkerRepository")
 */
class Worker
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\User", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\File(mimeTypes={ "application/pdf" })
     */
    private $cv;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Skills")
     */
    private $skills;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Apply", mappedBy="worker")
     */
    private $applies;

    private $skills_string;

    public function __construct()
    {
        $this->skills = new ArrayCollection();
        $this->applies = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getCv()
    {
        return $this->cv;
    }

    public function setCv($cv): self
    {
        $this->cv = $cv;

        return $this;
    }

    /**
     * @return Collection|Skills[]
     */
    public function getSkills(): Collection
    {
        return $this->skills;
    }

    public function addSkill(Skills $skill): self
    {
        if (!$this->skills->contains($skill)) {
            $this->skills[] = $skill;
        }

        return $this;
    }

    public function removeSkill(Skills $skill): self
    {
        if ($this->skills->contains($skill)) {
            $this->skills->removeElement($skill);
        }

        return $this;
    }

    public function clearSkills(): self
    {
        $this->skills = new ArrayCollection();

        return $this;
    }

    /**
     * @return Collection|Apply[]
     */
    public function getApplies(): Collection
    {
        return $this->applies;
    }

    public function addApply(Apply $apply): self
    {
        if (!$this->applies->contains($apply)) {
            $this->applies[] = $apply;
            $apply->setWorker($this);
        }

        return $this;
    }

    public function removeApply(Apply $apply): self
    {
        if ($this->applies->contains($apply)) {
            $this->applies->removeElement($apply);
            // set the owning side to null (unless already changed)
            if ($apply->getWorker() === $this) {
                $apply->setWorker(null);
            }
        }

        return $this;
    }

    public function getSkillsString($from = 'entity')
    {
        if($from !== 'entity')
            return $this->skills_string;
        $skills = [];
        foreach($this->getSkills() as $skill) {
            $skills[] = $skill->getName();
        }
        return implode(',',$skills);
    }

    public function setSkillsString(String $skillsString)
    {
        $this->skills_string = $skillsString;

        return $this;
    }
}

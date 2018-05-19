<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SkillsRepository")
 */
class Skills
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Worker", inversedBy="skills")
     */
    private $worker;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Project", inversedBy="skills")
     */
    private $project;

    public function __construct()
    {
        $this->worker = new ArrayCollection();
        $this->project = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection|Worker[]
     */
    public function getWorker(): Collection
    {
        return $this->worker;
    }

    public function addWorker(Worker $worker): self
    {
        if (!$this->worker->contains($worker)) {
            $this->worker[] = $worker;
        }

        return $this;
    }

    public function removeWorker(Worker $worker): self
    {
        if ($this->worker->contains($worker)) {
            $this->worker->removeElement($worker);
        }

        return $this;
    }

    /**
     * @return Collection|Project[]
     */
    public function getProject(): Collection
    {
        return $this->project;
    }

    public function addProject(Project $project): self
    {
        if (!$this->project->contains($project)) {
            $this->project[] = $project;
        }

        return $this;
    }

    public function removeProject(Project $project): self
    {
        if ($this->project->contains($project)) {
            $this->project->removeElement($project);
        }

        return $this;
    }
}

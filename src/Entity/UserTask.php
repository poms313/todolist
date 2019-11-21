<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserTaskRepository")
 */
class UserTask
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $taskId;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $taskName;

    /**
     * @ORM\Column(type="datetime")
     */
    private $taskStartDate;

    /**
     * @ORM\Column(type="datetime")
     */
    private $taskEndDate;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $taskDescription;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $taskCategory;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="userTasks")
     * @ORM\JoinColumn(nullable=false)
     */
    private $taskIdOwnerUser;

    /**
     * @ORM\Column(type="integer", options={"default" : 0})
     */
    private $taskNumberOfRemberEmail;

    /**
     * @ORM\Column(type="integer", options={"default" : 6})
     */
    private $taskNumberMaxEmail;

    /**
     * @ORM\Column(type="string", length=100, options={"default" : "En attente"})
     */
    private $taskStatut;


    public function getTaskId(): ?int
    {
        return $this->taskId;
    }


    public function getTaskName(): ?string
    {
        return $this->taskName;
    }

    public function setTaskName(string $taskName): self
    {
        $this->taskName = $taskName;

        return $this;
    }


    public function getTaskStartDate(): ?\DateTimeInterface
    {
        return $this->taskStartDate;
    }

    public function setTaskStartDate(\DateTimeInterface $taskStartDate): self
    {
        $this->taskStartDate = $taskStartDate;

        return $this;
    }


    public function getTaskEndDate(): ?\DateTimeInterface
    {
        return $this->taskEndDate;
    }

    public function setTaskEndDate(\DateTimeInterface $taskEndDate): self
    {
        if ($this->taskStartDate < $taskEndDate) {
            $this->taskEndDate = $taskEndDate;
        } else {
            $this->taskEndDate = $this->taskStartDate;
        }
        return $this;
    }


    public function getTaskDescription(): ?string
    {
        return $this->taskDescription;
    }

    public function setTaskDescription(?string $taskDescription): self
    {
        $this->taskDescription = $taskDescription;
        return $this;
    }


    public function getTaskCategory(): ?string
    {
        return $this->taskCategory;
    }

    public function setTaskCategory(?string $taskCategory): self
    {
        $this->taskCategory = $taskCategory;
        return $this;
    }


    public function getTaskIdOwnerUser(): ?User
    {
        return $this->taskIdOwnerUser;
    }

    public function setTaskIdOwnerUser(?User $taskIdOwnerUser): self
    {
        $this->taskIdOwnerUser = $taskIdOwnerUser;
        return $this;
    }


    public function getTaskNumberOfRemberEmail(): ?int
    {
        return $this->taskNumberOfRemberEmail;
    }

    public function setTaskNumberOfRemberEmail(?int $taskNumberOfRemberEmail): self
    {
        $this->taskNumberOfRemberEmail = $taskNumberOfRemberEmail;
        return $this;
    }


    public function getTaskNumberMaxEmail(): ?int
    {
        return $this->taskNumberMaxEmail;
    }

    public function setTaskNumberMaxEmail(?int $taskNumberMaxEmail): self
    {
        $this->taskNumberMaxEmail = $taskNumberMaxEmail;
        return $this;
    }


    public function getTaskStatut(): ?string
    {
        return $this->taskStatut;
    }

    public function setTaskStatut(?string $taskStatut): self
    {
        $this->taskStatut = $taskStatut;
        return $this;
    }
}

<?php
/*
*/ 
namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;



/**
 * @ORM\Table(name="app_users")
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(fields="email", message="Email déjà pris")
 * @UniqueEntity(fields="userName", message="Nom d'utilisateur déjà pris")
 * 
 */
class User implements UserInterface, \Serializable
{
    /**
     * @var int
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=50, unique=true)
     * @Assert\NotBlank()
     * @Assert\Length(
     *      min = 2,
     *      max = 50,
     *      minMessage = "Votre nom d-utilisateur doit être au minimun de {{ limit }} caractères",
     *      maxMessage = "Votre nom d-utilisateur doit être au maximum de {{ limit }} caractères"
     * )
     * @Assert\Type("string")
     */
    private $userName;

    /**
     * @var string
     * @ORM\Column(type="string", length=50, unique=true)
     * @Assert\NotBlank()
     * @Assert\Email(
     *     message = "L'email '{{ value }}' n'est pas une adresse mail valide.",
     * )
     * @Assert\Type("string")
     */
    private $email;

    /**
     * @var boolean
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isActive;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(
     *      min = 2,
     *      max = 255,
     *      minMessage = "Votre mot de passe doit être au minimun de {{ limit }} caractères",
     *      maxMessage = "Votre mot de passe doit être au maximum de {{ limit }} caractères"
     * )
     * @Assert\Type("string")
     * 
     */
    private $password;

    /**
     * @var string
     * 
     * @Assert\Length(max=250)
     * @Assert\Type("string")
     * @Assert\NotBlank()
     */
    private $plainPassword;

    /**
     * @var string
     * @ORM\Column(type="date", nullable=true)
     */
    private $birthdayDate;

      /**
     * @var string
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $accountCreatedDate;

    /**
     * @var string
     * @ORM\Column(type="string", length=15)
     * @Assert\Type("string")
     */
    private $userStatut;

    /**
     * @var array
     * @ORM\Column(type="array", nullable=true) 
     * @Assert\Type("array")
     */
    private $roles = [];

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\UserTask", mappedBy="taskIdOwnerUser", orphanRemoval=true)
     */
    private $userTasks;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $userPhoto;

    public function __construct()
    {
        $this->userTasks = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(): ?int
    {
        $this->id = $id;
    }

    public function getUserName(): ?string
    {
        return $this->userName;
    }

    public function setUserName(string $userName): self
    {
        $this->userName = $userName;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }


    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(?bool $isActive): self
    {
        $this->isActive = $isActive;
        return $this;
    }


    public function getBirthdayDate(): ?\DateTimeInterface
    {
        return $this->birthdayDate;
    }

    public function setBirthdayDate(?\DateTimeInterface $birthdayDate): self
    {
        $this->birthdayDate = $birthdayDate;
        return $this;
    }


    public function getAccountCreatedDate(): ?\DateTimeInterface
    {
        return $this->accountCreatedDate;
    }

    public function setAccountCreatedDate(?\DateTimeInterface $accountCreatedDate): self
    {
        $this->accountCreatedDate = $accountCreatedDate;
        return $this;
    }
    

    public function getUserStatut(): ?string
    {
        return $this->userStatut;
    }

    public function setUserStatut(string $userStatut): self
    {
        $this->userStatut = $userStatut;
        return $this;
    }



    public function getRoles(): array
    {
            if (empty($roles)) {
                $roles[] = 'ROLE_USER';
            }  
            return array_unique($this->roles);
    }

    public function setRoles(array $roles): void
    {
        $this->roles = $roles;
    }

    function addRole($role) 
    {
     //   $this->roles[] = $role;
    }


    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    function getPlainPassword()
    {
        return $this->plainPassword;
    }

    function setPlainPassword($plainPassword) 
    {
        $this->plainPassword = $plainPassword;
    }


    public function getSalt()
    {
        return null;
    }


    public function eraseCredentials()
    {
    }

    /** @see \Serializable::serialize() */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->userName,
            $this->password,
            // see section on salt below
            // $this->salt,
        ));
    }

    /** @see \Serializable::unserialize() */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->userName,
            $this->password,
            // see section on salt below
            // $this->salt
        ) = unserialize($serialized, array('allowed_classes' => false));
    }

    /**
     * @return Collection|UserTask[]
     */
    public function getUserTasks(): Collection
    {
        return $this->userTasks;
    }

    public function addUserTask(UserTask $userTask): self
    {
        if (!$this->userTasks->contains($userTask)) {
            $this->userTasks[] = $userTask;
            $userTask->setTaskIdOwnerUser($this);
        }
        return $this;
    }

    public function removeUserTask(UserTask $userTask): self
    {
        if ($this->userTasks->contains($userTask)) {
            $this->userTasks->removeElement($userTask);
            // set the owning side to null (unless already changed)
            if ($userTask->getTaskIdOwnerUser() === $this) {
                $userTask->setTaskIdOwnerUser(null);
            }
        }

        return $this;
    }

    public function getUserPhoto(): ?string
    {
        return $this->userPhoto;
    }

    public function setUserPhoto(?string $userPhoto): self
    {
        $this->userPhoto = $userPhoto;
        return $this;
    }
}



    

<?php

namespace App\Entity;

use App\Controller\MailController;
use App\Repository\UserRepository;
use App\Utils\Pesel;
use DateTimeImmutable;
use DateTimeZone;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $surname = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column(length: 11)]
    private ?string $pesel = null;

    #[ORM\Column]
    private ?bool $activated = null;

    #[ORM\Column(length: 255)]
    private ?string $source = null;

    #[ORM\OneToMany(mappedBy: 'userId', targetEntity: UserSkill::class, orphanRemoval: true)]
    private Collection $userSkills;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    public function __construct()
    {
        $this->userSkills = new ArrayCollection();
        $this->createdAt = new DateTimeImmutable('now', new DateTimeZone('Europe/Warsaw'));
        $this->activated = 0;
        $this->source = 'UI';
    }

    public function getId(): ?int
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

    public function getSurname(): ?string
    {
        return $this->surname;
    }

    public function setSurname(string $surname): self
    {
        $this->surname = $surname;

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

    public function getPesel(): ?string
    {
        return $this->pesel;
    }

    public function setPesel(string $pesel): self
    {
        $this->pesel = $pesel;

        $this->age = Pesel::getAge($pesel);

        if ($this->age > 18) {
            $this->setActivated(1);
        }

        return $this;
    }

    public function isActivated(): ?bool
    {
        return $this->activated;
    }

    public function getActivatedLabel(): string
    {
        return $this->activated ? 'active':'not active';
    }

    public function setActivated(bool $activated): self
    {
        $this->activated = $activated;

        if ($activated) {
            MailController::sendUserActivationEmail($this);
        }

        return $this;
    }

    public function getSource(): ?string
    {
        return $this->source;
    }

    public function setSource(string $source): self
    {
        $this->source = $source;

        return $this;
    }

    /**
     * @return Collection<int, UserSkill>
     */
    public function getUserSkills(): Collection
    {
        return $this->userSkills;
    }

    public function addUserSkill(UserSkill $userSkill): self
    {
        if (!$this->userSkills->contains($userSkill)) {
            $this->userSkills->add($userSkill);
            $userSkill->setUserId($this);
        }

        return $this;
    }

    public function removeUserSkill(UserSkill $userSkill): self
    {
        if ($this->userSkills->removeElement($userSkill)) {
            // set the owning side to null (unless already changed)
            if ($userSkill->getUserId() === $this) {
                $userSkill->setUserId(null);
            }
        }

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getAge(): ?int
    {
        if ($this->pesel) {
            return Pesel::getAge($this->pesel);
        }
        return 0;
    }

    public function getMissingAdolescenceText()
    {
        $missingInterval = Pesel::getAdolescentInterval($this->pesel);

        if (Pesel::getAge($this->pesel) < 18) {

            $years = $missingInterval->y > 0 ? $missingInterval->y . ' y' : '';
            $months = $missingInterval->m > 0 ? $missingInterval->m . ' m' : '';
            $days = $missingInterval->d > 0 ? $missingInterval->d . ' days' : '';
            return '- ' . $years . ' ' . $months . ' ' . $days;
        }
        return '';
    }
}

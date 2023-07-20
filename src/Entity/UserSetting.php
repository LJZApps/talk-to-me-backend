<?php

namespace App\Entity;

use App\Repository\UserSettingRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserSettingRepository::class)]
class UserSetting
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToMany(mappedBy: 'userSetting', targetEntity: User::class, orphanRemoval: true)]
    private Collection $user;

    #[ORM\Column]
    private ?bool $mark_messages_as_read = true;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?UserInformationPolicy $information_policy = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?UserStatusPolicy $status_policy = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?UserMessagePolicy $message_policy = null;

    public function __construct()
    {
        $this->user = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUser(): Collection
    {
        return $this->user;
    }

    public function addUser(User $user): static
    {
        if (!$this->user->contains($user)) {
            $this->user->add($user);
            $user->setUserSetting($this);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        if ($this->user->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getUserSetting() === $this) {
                $user->setUserSetting(null);
            }
        }

        return $this;
    }

    public function isMarkMessagesAsRead(): ?bool
    {
        return $this->mark_messages_as_read;
    }

    public function setMarkMessagesAsRead(bool $mark_messages_as_read): static
    {
        $this->mark_messages_as_read = $mark_messages_as_read;

        return $this;
    }

    public function getInformationPolicy(): ?UserInformationPolicy
    {
        return $this->information_policy;
    }

    public function setInformationPolicy(UserInformationPolicy $information_policy): static
    {
        $this->information_policy = $information_policy;

        return $this;
    }

    public function getStatusPolicy(): ?UserStatusPolicy
    {
        return $this->status_policy;
    }

    public function setStatusPolicy(UserStatusPolicy $status_policy): static
    {
        $this->status_policy = $status_policy;

        return $this;
    }

    public function getMessagePolicy(): ?UserMessagePolicy
    {
        return $this->message_policy;
    }

    public function setMessagePolicy(UserMessagePolicy $message_policy): static
    {
        $this->message_policy = $message_policy;

        return $this;
    }
}

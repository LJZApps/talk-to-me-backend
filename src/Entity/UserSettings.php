<?php

namespace App\Entity;

use App\Repository\UserSettingsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\DocBlock\Tags\Factory\StaticMethod;

#[ORM\Entity(repositoryClass: UserSettingsRepository::class)]
class UserSettings
{
    #[ORM\Id]
    #[ORM\Column(length: 40)]
    private ?string $uid = null;

    #[ORM\Column]
    private array $roles = ["ROLE_USER", "ROLE_ADMIN", "ROLE_MODERATOR", "ROLE_DEVELOPER", "ROLE_COMPANY", "ROLE_CREATOR"];

    #[ORM\Column]
    private ?bool $mark_messages_as_read = true;

    #[ORM\Column]
    private ?bool $messages_open = true;

    #[ORM\Column]
    private ?bool $public_informations = true;

    #[ORM\Column]
    private ?bool $private_profile = false;

    #[ORM\Column]
    private ?int $last_online_visibility = 0;

    #[ORM\Column]
    private ?bool $verified = false;

    public function getUid(): ?string
    {
        return $this->uid;
    }

    public function setUid(string $uid): static
    {
        $this->uid = $uid;

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

    public function isMessagesOpen(): ?bool
    {
        return $this->messages_open;
    }

    public function setMessagesOpen(bool $messages_open): static
    {
        $this->messages_open = $messages_open;

        return $this;
    }

    public function isPublicInformations(): ?bool
    {
        return $this->public_informations;
    }

    public function setPublicInformations(bool $public_informations): static
    {
        $this->public_informations = $public_informations;

        return $this;
    }

    public function isPrivateProfile(): ?bool
    {
        return $this->private_profile;
    }

    public function setPrivateProfile(bool $private_profile): static
    {
        $this->private_profile = $private_profile;

        return $this;
    }

    public function getLastOnlineVisibility(): ?int
    {
        return $this->last_online_visibility;
    }

    public function setLastOnlineVisibility(int $last_online_visibility): static
    {
        $this->last_online_visibility = $last_online_visibility;

        return $this;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    public function isVerified(): ?bool
    {
        return $this->verified;
    }

    public function setVerified(bool $verified): static
    {
        $this->verified = $verified;

        return $this;
    }
}

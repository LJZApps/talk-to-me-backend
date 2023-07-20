<?php

namespace App\Entity;

use App\Repository\UserInformationPolicyRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserInformationPolicyRepository::class)]
class UserInformationPolicy
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column]
    private ?bool $allow_everyone = true;

    #[ORM\Column]
    private ?bool $allow_only_friends = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function isAllowEveryone(): ?bool
    {
        return $this->allow_everyone;
    }

    public function setAllowEveryone(bool $allow_everyone): static
    {
        $this->allow_everyone = $allow_everyone;

        return $this;
    }

    public function isAllowOnlyFriends(): ?bool
    {
        return $this->allow_only_friends;
    }

    public function setAllowOnlyFriends(bool $allow_only_friends): static
    {
        $this->allow_only_friends = $allow_only_friends;

        return $this;
    }
}

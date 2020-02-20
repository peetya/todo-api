<?php declare(strict_types=1);

namespace App\Entity;

use Symfony\Component\Security\Core\User\UserInterface;

class User implements UserInterface
{
    private int $id;

    private string $username;

    private string $password;

    private string $firstName;

    private string $lastName;

    private array $roles;

    public function getId(): int
    {
        return $this->id;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): User
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): User
    {
        $this->password = $password;

        return $this;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): User
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): User
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;

        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): User
    {
        $this->roles = $roles;

        return $this;
    }

    public function getSalt(): void
    {
        return;
    }

    public function eraseCredentials(): void
    {
        return;
    }
}

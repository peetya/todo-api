<?php declare(strict_types=1);

namespace App\Tests\Unit\Entity;

use App\Entity\User;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\User\UserInterface;

class UserTest extends TestCase
{
    private User $subject;

    protected function setUp(): void
    {
        $this->subject = new User();
    }

    public function testInterface(): void
    {
        $this->assertInstanceOf(UserInterface::class, $this->subject);
    }

    public function testUsernameSetterGetter(): void
    {
        $this->assertSame($this->subject, $this->subject->setUsername('username'));
        $this->assertSame('username', $this->subject->getUsername());
    }

    public function testPasswordSetterGetter(): void
    {
        $this->assertSame($this->subject, $this->subject->setPassword('password'));
        $this->assertSame('password', $this->subject->getPassword());
    }

    public function testFirstNameSetterGetter(): void
    {
        $this->assertSame($this->subject, $this->subject->setFirstName('firstName'));
        $this->assertSame('firstName', $this->subject->getFirstName());
    }

    public function testLastNameSetterGetter(): void
    {
        $this->assertSame($this->subject, $this->subject->setLastName('lastName'));
        $this->assertSame('lastName', $this->subject->getLastName());
    }

    public function testRolesSetterGetter(): void
    {
        $this->assertSame($this->subject, $this->subject->setRoles(['ROLE_FOO']));
        $this->assertSame(['ROLE_FOO', 'ROLE_USER'], $this->subject->getRoles());
    }

    public function testGetSalt(): void
    {
        $this->assertNull($this->subject->getSalt());
    }

    public function testEraseCredentials(): void
    {
        $this->assertNull($this->subject->eraseCredentials());
    }
}

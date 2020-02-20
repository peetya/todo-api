<?php declare(strict_types=1);

namespace App\Tests\Integration\Repository;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserRepositoryTest extends KernelTestCase
{
    private UserRepository $subject;

    protected function setUp(): void
    {
        static::bootKernel();

        $this->subject = static::$container->get(UserRepository::class);
    }

    public function testRepositoryEntity(): void
    {
        $this->assertSame(User::class, $this->subject->getClassName());
    }
}

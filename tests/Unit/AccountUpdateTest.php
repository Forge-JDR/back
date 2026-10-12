<?php

namespace App\Tests\Unit;

use App\Controller\UserController;
use App\Entity\User;
use App\Repository\UserRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Serializer\SerializerInterface;

// Bypass the framework response serializer and authorization infrastructure only.
// The real updateUser method is exercised without a database or network access.
class AccountUpdateController extends UserController
{
    public function __construct(User $user)
    {
        $storage = new TokenStorage();
        $storage->setToken(new UsernamePasswordToken($user, 'api', ['ROLE_USER']));
        $property = new \ReflectionProperty(UserController::class, 'tokenStorageInterface');
        $property->setValue($this, $storage);
    }

    protected function denyAccessUnlessGranted(mixed $attribute, mixed $subject = null, string $message = 'Access Denied.'): void
    {
    }

    protected function json(mixed $data, int $status = 200, array $headers = [], array $context = []): JsonResponse
    {
        return new JsonResponse($data instanceof User ? ['pseudo' => $data->getPseudo()] : $data, $status, $headers);
    }
}

class AccountUpdateTest extends TestCase
{
    private function update(array $data, User $user, UserRepository $repository, UserPasswordHasherInterface $hasher): JsonResponse
    {
        $serializer = $this->createMock(SerializerInterface::class);
        $serializer->expects($this->never())->method('deserialize');
        $request = Request::create('/api/me', 'PUT', [], [], [], ['CONTENT_TYPE' => 'application/json'], json_encode($data));
        return (new AccountUpdateController($user))->updateUser($request, $repository, $hasher, $serializer);
    }

    public function testClientCannotChangeRoles(): void
    {
        $user = (new User())->setRoles(['ROLE_USER']);
        $repository = $this->createMock(UserRepository::class);
        $repository->expects($this->never())->method('updateUser');
        $response = $this->update(['roles' => ['ROLE_ADMIN']], $user, $repository, $this->createMock(UserPasswordHasherInterface::class));
        self::assertSame(400, $response->getStatusCode());
        self::assertNotContains('ROLE_ADMIN', $user->getRoles());
    }

    public function testWrongCurrentPasswordDoesNotChangePassword(): void
    {
        $user = (new User())->setPassword('old-hash');
        $repository = $this->createMock(UserRepository::class);
        $repository->expects($this->never())->method('updateUser');
        $hasher = $this->createMock(UserPasswordHasherInterface::class);
        $hasher->method('isPasswordValid')->willReturn(false);
        $hasher->expects($this->never())->method('hashPassword');
        $response = $this->update(['password' => 'new-password', 'currentPassword' => 'wrong'], $user, $repository, $hasher);
        self::assertSame(400, $response->getStatusCode());
        self::assertSame('old-hash', $user->getPassword());
    }

    public function testPseudoUpdatePreservesRoles(): void
    {
        $user = (new User())->setRoles(['ROLE_USER'])->setPseudo('before');
        $repository = $this->createMock(UserRepository::class);
        $repository->method('findOneBy')->willReturn(null);
        $repository->expects($this->once())->method('updateUser')->with($user);
        $response = $this->update(['pseudo' => ' after '], $user, $repository, $this->createMock(UserPasswordHasherInterface::class));
        self::assertSame(200, $response->getStatusCode());
        self::assertSame('after', $user->getPseudo());
        self::assertNotContains('ROLE_ADMIN', $user->getRoles());
    }
}

<?php

namespace App\Service;

use App\Entity\User;
use App\Exception\MissingFieldsException;
use App\Exception\UniqueFieldConflictException;
use App\Repository\UserRepository;
use Cassandra\Exception\UnauthorizedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class UserService
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly UserRepository $userRepository
    )
    {
    }

    /**
     * @param Request $request
     * @return User
     * @throws MissingFieldsException if required fields are missing
     * @throws UniqueFieldConflictException if other are other users with same username/email
     */
    public function createUser(Request $request): User
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['username']) || !isset($data['password']) || !isset($data['email'])) {
            throw new MissingFieldsException();
        }

        $conflictingFields = [];
        if (!empty($this->userRepository->findBy(["email" => $data['email']]))) {
            $conflictingFields[] = "email";
        }
        if (!empty($this->userRepository->findBy(["username" => $data['username']]))) {
            $conflictingFields[] = "username";
        }

        if (!empty($conflictingFields)) {
            throw new UniqueFieldConflictException($conflictingFields);
        }

        $user = new User();
        $user->setUsername($data['username']);
        $user->setEmail($data['email']);

        $hashedPassword = $this->passwordHasher->hashPassword($user, $data['password']);
        $user->setPassword($hashedPassword);

        return $user;
    }

    /**
     * @param string $identifier
     * @return User|null
     */
    private function getUserOnIdentifier(string $identifier): ?User
    {
        $user = $this->userRepository->findOneBy(["username" => $identifier]);
        if( $user == null ) {
            $user = $this->userRepository->findOneBy(["email" => $identifier]);
        }

        return $user;
    }

    /**
     * @param Request $request
     * @return User
     * @throws AuthenticationException if the credentials are invalid
     */
    public function checkCredentials(Request $request): User{
        $data = json_decode($request->getContent(), true);

        if (!isset($data['identifier']) || !isset($data['password'])) {
            throw new MissingFieldsException();
        }

        $user = $this->getUserOnIdentifier($data['identifier']);
        if ($user == null || !$this->passwordHasher->isPasswordValid($user, $data['password'])) {
            throw new AuthenticationException();
        }

        return $user;
    }
}
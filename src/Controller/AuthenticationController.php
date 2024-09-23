<?php

namespace App\Controller;

use App\Exception\MissingFieldsException;
use App\Exception\UniqueFieldConflictException;
use App\Repository\UserRepository;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Throwable;

class AuthenticationController extends AbstractController
{
    public function __construct(
        private readonly UserService    $userService,
        private readonly UserRepository $userRepository,
    )
    {
    }

    #[Route('/register', name: 'app_register', methods: ["POST"])]
    public function register(Request $request): JsonResponse
    {
        try {
            $user = $this->userService->createUser($request);
            $this->userRepository->save($user);
        } catch (MissingFieldsException $e) {
            return $this->json(
                ['error' => $e->getMessage()],
                Response::HTTP_BAD_REQUEST
            );
        } catch (UniqueFieldConflictException $e) {
            return $this->json([
                'error' => 'Some fields are already taken',
                'conflictingFields' => $e->getConflictingFields()
            ], Response::HTTP_CONFLICT);
        } catch (Throwable $e) {
            return $this->json(
                ['error' => 'Internal server error'],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        return $this->json(
            ['message' => 'User successfully registered!'],
            Response::HTTP_CREATED
        );
    }

    #[Route('/login', name: 'app_login', methods: ["POST"])]
    public function login(Request $request): JsonResponse
    {
        try{
            $user = $this->userService->checkCredentials($request);
        } catch (AuthenticationException $e) {
            return $this->json(
                ['error' => 'Invalid credentials'],
                Response::HTTP_UNAUTHORIZED
            );
        }

        return $this->json(
            ['message' => 'User successfully registered!'],
            Response::HTTP_CREATED
        );
    }
}

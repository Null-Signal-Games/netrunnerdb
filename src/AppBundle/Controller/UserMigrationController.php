<?php

namespace AppBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

/**
 * Class UserMigrationController
 * @package AppBundle\Controller
 *
 * Provides JSON endpoints to power user migration to Keycloak.
 *
 * Requests are protected by a configured Bearer token.
 */
class UserMigrationController extends Controller
{
    /** @var EntityManagerInterface $entityManager */
    protected $entityManager;
    /** @var LoggerInterface $logger */
    protected $logger;
    /** @var EncoderFactoryInterface $factory */
    protected $factory;

    public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger, EncoderFactoryInterface $factory)
    {
      $this->entityManager = $entityManager;
      $this->logger = $logger;
      $this->factory = $factory;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getAction(string $username, Request $request)
    {
        $errorResponse = $this->verifyRequest();

        if ($errorResponse > 0) {
            return new JsonResponse([], $errorResponse);
        }

        $user = $this->entityManager->getRepository('AppBundle:User')->findOneBy(['username' => $username]);
        if (!$user) {
            // User not found, return 404 Not Found
            return new JsonResponse([], Response::HTTP_NOT_FOUND);
        }

        // Things are good, so let's go find us a user.
        $data = [
            // Set to Null to force Keycloak to generate a new ID for the user.
            'id' => null,
            'username' => $user->getUsername(),
            'email' => $user->getEmail(),
            // NRDB Classic does not have first/last name fields
            'firstName' => null,
            'lastName' => null,
            'enabled' => $user->isEnabled(),
            'emailVerified' => false,
            'attributes' => [
                'faction' => [$user->getFaction()],
                'legacy_id' => [$user->getId()],
            ],
            'requiredActions' => [
                'VERIFY_EMAIL',
                'UPDATE_PROFILE',
            ],
            // TODO(plural): Make role mappings for user types in NRDB Classic.
        ];

        // We found the user so return OK and the data!
        return new JsonResponse($data, Response::HTTP_OK);
    }

    public function postAction(string $username, Request $request)
    {
        $errorResponse = $this->verifyRequest();

        if ($errorResponse > 0) {
            return new JsonResponse([], $errorResponse);
        }

        // Make sure the request contains a password
        $requestJsonBody = $request->getContent();
        $requestContent = json_decode($requestJsonBody, true);
        if (!isset($requestContent['password']) || empty($requestContent['password'])) {
            return new JsonResponse([], Response::HTTP_BAD_REQUEST);
        }

        // Things are good, so let's go find us a user.
        $user = $this->entityManager->getRepository('AppBundle:User')->findOneBy(['username' => $username]);
        if (!$user) {
            // User not found, return 404 Not Found
            return new JsonResponse([], Response::HTTP_NOT_FOUND);
        }

        // User was found, let's check their password
        $encoder = $this->factory->getEncoder($user);
        $salt = $user->getSalt();
        if(!$encoder->isPasswordValid($user->getPassword(), $requestContent['password'], $salt)) {
            return new JsonResponse(['message'=> 'Invalid password'], Response::HTTP_NOT_FOUND);
        }

        $data = [
            'username' => $user->getUsername(),
        ];

        return new JsonResponse($data, Response::HTTP_OK);
    }

    private function verifyRequest(): int {
        // Check for proper Bearer token, return 401 Unauthorized if missing or invalid.
        $headers = getallheaders();
        $authorizationHeader = $headers['Authorization'] ?? null;

        if (!$authorizationHeader || !str_starts_with($authorizationHeader, 'Bearer ')) {
            return Response::HTTP_UNAUTHORIZED;
        }
        // Remove "Bearer " prefix
        $token = substr($authorizationHeader, 7);
        // TODO(plural): Make the token a configurable parameter
        if ($token !== $this->getParameter('user_migration_token')) {
            return Response::HTTP_UNAUTHORIZED;
        }

        return 0;
    }
}

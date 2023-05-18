<?php

namespace App\Tests;

use App\Domain\Auth\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class WebTestCase extends \Symfony\Bundle\FrameworkBundle\Test\WebTestCase
{
    protected KernelBrowser $client;
    protected EntityManagerInterface $em;
    protected ?SessionInterface $session = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = self::createClient();
        /** @var EntityManagerInterface $em */
        $em = self::getContainer()->get(EntityManagerInterface::class);
        $this->em = $em;
        $this->em->getConnection()->getConfiguration()->setMiddlewares([]);
        parent::setUp();
    }

    protected function tearDown(): void
    {
        $this->em->clear();
        parent::tearDown();
    }

    public function jsonRequest(string $method, string $url, ?array $data = null): string
    {
        $this->client->request($method, $url, [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], $data ? json_encode($data, JSON_THROW_ON_ERROR) : null);

        return $this->client->getResponse()->getContent();
    }

    public function expectAlert(string $type, ?string $message = null): void
    {
        $this->assertEquals(
            1,
            $this
                ->client
                ->getCrawler()
                ->filter("alert-message[type=\"$type\"]")
                ->count()
        );
    }

    public function expectFormErrors(int $expectedErrors): void
    {
        $this->assertEquals(
            $expectedErrors,
            $this
                ->client
                ->getCrawler()
                ->filter('.form-error p')
                ->count(),
            'Form errors mismatch.'
        );
    }

    public function login(?User $user)
    {
        if (null === $user) {
            return;
        }
        $this->client->loginUser($user);
    }
}

<?php
namespace App\Tests\Integration;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MvpTest extends WebTestCase
{
    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $em = static::getContainer()->get(EntityManagerInterface::class);
        // Only the isolated SQLite test connection is allowed to be reset.
        self::assertSame('pdo_sqlite', $em->getConnection()->getParams()['driver']);
        $metadata = $em->getMetadataFactory()->getAllMetadata();
        $tool = new SchemaTool($em);
        $tool->dropSchema($metadata);
        $tool->createSchema($metadata);
    }

    private function call(string $method, string $path, array $body = [], ?string $token = null, int $status = 200): array
    {
        $headers = ['CONTENT_TYPE' => 'application/json', 'HTTP_ACCEPT' => 'application/json'];
        if ($token) { $headers['HTTP_AUTHORIZATION'] = 'Bearer '.$token; }
        $this->client->request($method, $path, [], [], $headers, json_encode($body));
        self::assertResponseStatusCodeSame($status, $this->client->getResponse()->getContent());
        return json_decode($this->client->getResponse()->getContent(), true) ?? [];
    }

    private function account(string $pseudo): array
    {
        $body = ['username' => $pseudo.'@example.com', 'pseudo' => $pseudo, 'password' => 'long-password-123'];
        $this->call('POST', '/register', $body, null, 201);
        unset($body['pseudo']);
        return $this->call('POST', '/api/login_check', $body);
    }

    public function testCompleteWorkflowAndPreservationOfExistingSheets(): void
    {
        $session = $this->account('author');
        self::assertArrayHasKey('refresh_token', $session);
        $token = $session['token'];
        $fields = [['key' => 'strength', 'label' => 'Force', 'type' => 'number'], ['key' => 'alive', 'label' => 'Vivant', 'type' => 'boolean']];
        $rpg = $this->call('POST', '/api/mvp/rpgs', ['name' => 'Mon univers', 'description' => str_repeat('a', 4000), 'fields' => $fields], $token, 201);
        $party = $this->call('POST', '/api/mvp/campaigns', ['rpgId' => $rpg['id'], 'name' => 'La traversée'], $token, 201);
        $sheet = $this->call('POST', '/api/mvp/campaigns/'.$party['id'].'/sheets', ['name' => 'Ari', 'values' => ['strength' => 12, 'alive' => true]], $token, 201);
        $this->call('POST', '/api/mvp/campaigns/'.$party['id'].'/sheets', ['name' => 'Bryn', 'values' => ['strength' => 8, 'alive' => false]], $token, 201);
        $changed = $this->call('PUT', '/api/mvp/rpgs/'.$rpg['id'], ['name' => 'Mon univers', 'fields' => [], 'expectedTemplateVersion' => $rpg['templateVersion']], $token);
        self::assertGreaterThan($rpg['templateVersion'], $changed['templateVersion']);
        $this->call('PUT', '/api/mvp/rpgs/'.$rpg['id'], ['name' => 'Modification périmée', 'fields' => [], 'expectedTemplateVersion' => $rpg['templateVersion']], $token, 409);
        $this->call('PUT', '/api/mvp/sheets/'.$sheet['id'], ['name' => 'Ari', 'values' => ['strength' => 13, 'alive' => true]], $token);
        $this->call('PUT', '/api/mvp/campaigns/'.$party['id'], ['name' => 'La traversée', 'notes' => 'La porte est ouverte.'], $token);
        $this->call('POST', '/api/mvp/campaigns/'.$party['id'].'/sessions', ['title' => 'Le départ', 'date' => '2026-10-12', 'notes' => 'Rencontre au port.'], $token, 201);
        $this->call('POST', '/api/mvp/campaigns/'.$party['id'].'/sessions', ['title' => 'Date invalide', 'date' => '2026-02-31'], $token, 400);
        $freshSession = $this->call('POST', '/api/token/refresh', ['refresh_token' => $session['refresh_token']]);
        $reloaded = $this->call('GET', '/api/mvp/campaigns/'.$party['id'], [], $freshSession['token']);
        self::assertCount(2, $reloaded['sheets']);
        self::assertSame($fields, $reloaded['sheets'][0]['fields']);
        self::assertSame(13, $reloaded['sheets'][0]['values']['strength']);
        self::assertSame('La porte est ouverte.', $reloaded['notes']);
        self::assertCount(1, $reloaded['journal']);
    }

    public function testPrivateResourcesAndRolesCannotBeChangedByAnotherUser(): void
    {
        $a = $this->account('first')['token'];
        $rpg = $this->call('POST', '/api/mvp/rpgs', ['name' => 'Privé'], $a, 201);
        $party = $this->call('POST', '/api/mvp/campaigns', ['rpgId' => $rpg['id'], 'name' => 'Privée'], $a, 201);
        $sheet = $this->call('POST', '/api/mvp/campaigns/'.$party['id'].'/sheets', ['name' => 'Privé'], $a, 201);
        $b = $this->account('second')['token'];
        self::assertSame([], $this->call('GET', '/api/mvp/campaigns', [], $b));
        self::assertSame([], $this->call('GET', '/api/mvp/rpgs', [], $b));
        $this->call('GET', '/api/mvp/campaigns/'.$party['id'], [], $b, 404);
        $this->call('PUT', '/api/mvp/campaigns/'.$party['id'], ['name' => 'Volée'], $b, 404);
        $this->call('PUT', '/api/mvp/sheets/'.$sheet['id'], ['name' => 'Volé'], $b, 404);
        $this->call('POST', '/api/mvp/campaigns', ['rpgId' => $rpg['id'], 'name' => 'Volée'], $b, 404);
        $this->call('PUT', '/api/me', ['roles' => ['ROLE_ADMIN']], $b, 400);
        $this->call('GET', '/api/admin/wikis', [], $b, 403);
        $this->call('GET', '/api/mvp/rpgs', [], null, 401);
    }

    public function testInvalidValuesAndRegistrationPayloadAreRejected(): void
    {
        $this->call('POST', '/register', ['username' => 'wrong', 'pseudo' => 'x', 'password' => 'short'], null, 400);
        $this->call('POST', '/register', ['username' => 'valid@example.com', 'pseudo' => 'x', 'password' => 'valid-password', 'roles' => ['ROLE_ADMIN']], null, 400);
        $token = $this->account('valid')['token'];
        $this->call('POST', '/api/mvp/rpgs', ['name' => 'X', 'fields' => [['key' => 'x', 'label' => 'X', 'type' => 'script']]], $token, 400);
        $rpg = $this->call('POST', '/api/mvp/rpgs', ['name' => 'X', 'fields' => [['key' => 'health', 'label' => 'PV', 'type' => 'number']]], $token, 201);
        $party = $this->call('POST', '/api/mvp/campaigns', ['name' => 'X', 'rpgId' => $rpg['id']], $token, 201);
        $this->call('POST', '/api/mvp/campaigns/'.$party['id'].'/sheets', ['name' => 'X', 'values' => ['health' => '12']], $token, 400);
        $this->call('PUT', '/api/me', ['password' => 'new-password', 'currentPassword' => 'wrong'], $token, 400);
        $this->call('PUT', '/api/me', ['password' => 'new-password', 'currentPassword' => 'long-password-123'], $token);
        $this->call('POST', '/api/login_check', ['username' => 'valid@example.com', 'password' => 'new-password']);
    }
    public function testLegacyEndpointsCannotTransferOwnershipOrPublishDirectly(): void
    {
        $token = $this->account('legacy')['token'];
        $wiki = $this->call('POST', '/api/wikis', ['name' => 'Historique', 'content' => 'Contenu', 'Status' => 'published', 'user' => ['id' => 999]], $token);
        self::assertSame('inProgress', $wiki['Status']);
        $this->call('PUT', '/api/wikis/'.$wiki['id'], ['Status' => 'published'], $token, 403);
        $this->call('PUT', '/api/wikis/'.$wiki['id'], ['user' => ['id' => 999]], $token, 400);
        $updated = $this->call('PUT', '/api/wikis/'.$wiki['id'], ['name' => 'Nouveau nom'], $token);
        self::assertSame('Nouveau nom', $updated['Name']);
        self::assertCount(1, $this->call('GET', '/api/mvp/rpgs', [], $token));
    }

}

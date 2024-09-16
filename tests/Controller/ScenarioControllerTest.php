<?php  

namespace App\Tests;

use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;

class ScenarioControllerTest extends TestCase
{
    private $client;
    private $id;
    private $token;

    protected function setUp(): void
    {
        $baseUri = 'https://backforgejdryr5nsvrg-container-back-forge-jdr.functions.fnc.fr-par.scw.cloud'; // Remplacez par l'URL de base correcte
        $this->client = new Client(['base_uri' => $baseUri]);

        // Récupérer un token JWT valide pour les tests
        $this->token = $this->getToken();
    }

    private function getToken(): string
    {
        $response = $this->client->request('POST', '/api/login_check', [
            'json' => [
                'username' => 'admin@mail.com',
                'password' => 'adminmdp123'
            ]
        ]);

        $this->assertEquals(200, $response->getStatusCode());
        $tokenData = json_decode($response->getBody(), true);
        $this->assertArrayHasKey('token', $tokenData);
        return $tokenData['token'];
    }

    public function testCreateScenario()
    {
        $response = $this->client->request('POST', '/api/wikis/1/scenarios', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token,
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'name' => 'titre de scenario',
                'content' => 'mon histoire',
            ]
        ]);

        $this->assertEquals(201, $response->getStatusCode());

        $responseContent = json_decode($response->getBody(), true);
        $this->assertArrayHasKey('id', $responseContent);
        $this->id = $responseContent['id']; // Sauvegarder l'ID pour la modification
    }

    public function testUpdateScenario()
    {
        // Assurez-vous que $this->id est défini et valide avant d'exécuter ce test
        $this->assertNotNull($this->id, 'ID is not set from previous test');

        $response = $this->client->request('PUT', "/api/wikis/1/scenarios/{$this->id}", [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token,
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'name' => 'titre de scenario changé',
                'content' => 'mon histoire modifié',
            ]
        ]);

        $this->assertEquals(200, $response->getStatusCode()); // Ajustez selon la spécification

        $responseContent = json_decode($response->getBody(), true);
        $this->assertArrayHasKey('name', $responseContent);
        $this->assertEquals('titre de scenario changé', $responseContent['name']);
    }
}
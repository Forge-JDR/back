<?php  

namespace App\Tests;

use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;

class JobControllerTest extends TestCase
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

    public function testCreateUpdateDeleteJob()
    {

        // Creation d'un job
        $response = $this->client->request('POST', '/api/wikis/15/jobs', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token,
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'name' => 'titre du job',
                'content' => 'mon histoire',
            ]
        ]);

        $this->assertEquals(200, $response->getStatusCode()); // Vérifie le code renvoyé

        // Récupère l'ID
        $responseContent = json_decode($response->getBody(), true);
        $jobs = $responseContent['Jobs'];
        usort($jobs, function($a, $b) {
            return strtotime($b['createdAt']) - strtotime($a['createdAt']);
        });
        $dernierJobsCree = $jobs[0];
        $this->id = $dernierJobsCree['id'];
        


        // Modification du job
        $response = $this->client->request('PUT', "/api/wikis/15/jobs/{$this->id}", [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token,
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'name' => 'titre du job changé',
                'description' => 'mon histoire modifié',
            ]
        ]);

        $this->assertEquals(200, $response->getStatusCode()); // Vérifie le code renvoyé

        // Récupère l'ID
        $responseContent = json_decode($response->getBody(), true);
        $jobs = $responseContent['Jobs'];
        usort($jobs, function($a, $b) {
            return strtotime($b['createdAt']) - strtotime($a['createdAt']);
        });
        $dernierJobsCree = $jobs[0];
        $name = $dernierJobsCree['name'];
        // Vérifie la modification des valeur
        $this->assertEquals('titre du job changé', $name);
        


        // Suppression du job
        $response = $this->client->request('DELETE', "/api/wikis/15/jobs/{$this->id}", [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token,
                'Content-Type' => 'application/json',
            ]
        ]);

        $this->assertEquals(200, $response->getStatusCode()); // Vérifie le code renvoyé
    }
}
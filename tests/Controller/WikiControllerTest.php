<?php  

namespace App\Tests;

use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;

class WikiControllerTest extends TestCase
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

    public function testCreateGetUpdateDeleteWiki()
    {

        // Creation d'un wiki
        $response = $this->client->request('POST', '/api/wikis', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token,
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'name' => 'Mon wiki a moi',
                'content' => 'l\'histoire de mon wiki',
            ]
        ]);

        $this->assertEquals(200, $response->getStatusCode()); // Vérifie le code renvoyé

        // Vérifie l'ajout des valeur
        $responseContent = json_decode($response->getBody(), true);
        $this->assertArrayHasKey('id', $responseContent);
        $this->id = $responseContent['id']; // Sauvegarder l'ID pour la modification

        // Recherche de tout les WIKIs
        $response = $this->client->request('GET', '/api/wikis', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token,
                'Content-Type' => 'application/json',
            ]
        ]);

        $this->assertEquals(200, $response->getStatusCode()); // Vérifie le code renvoyé

        // Recherche d'un WIKI
        $response = $this->client->request('GET', "/api/wikis/{$this->id}", [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token,
                'Content-Type' => 'application/json',
            ]
        ]);

        $this->assertEquals(200, $response->getStatusCode()); // Vérifie le code renvoyé
        
        // Modification du wiki
        $response = $this->client->request('PUT', "/api/wikis/{$this->id}", [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token,
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'name' => 'c\'est toujours mon wiki a moi',
                'content' => 'l\'histoire de mon wiki évolue',
            ]
        ]);

        $this->assertEquals(200, $response->getStatusCode()); // Vérifie le code renvoyé

        // Vérifie la modification des valeur
        $responseContent = json_decode($response->getBody(), true);
        $this->assertArrayHasKey('Name', $responseContent);
        $this->assertEquals('c\'est toujours mon wiki a moi', $responseContent['Name']);
        


        // Suppression du wiki
        $response = $this->client->request('DELETE', "/api/wikis/{$this->id}", [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token,
                'Content-Type' => 'application/json',
            ]
        ]);

        $this->assertEquals(204, $response->getStatusCode()); // Vérifie le code renvoyé
    }
}
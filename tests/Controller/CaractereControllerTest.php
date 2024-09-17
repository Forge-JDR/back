<?php  

namespace App\Tests;

use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;

class CaractereControllerTest extends TestCase
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

    public function testCaractere()
    {
         // Creation d'un caractere
         $response = $this->client->request('POST', '/api/caracters', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token,
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'name' => 'Khrone Ikels',
                'Content' => 'BEST PALADIN EVER',
            ]
        ]);

        $this->assertEquals(200, $response->getStatusCode()); // Vérifie le code renvoyé

        // Vérifie l'ajout des valeur
        $responseContent = json_decode($response->getBody(), true);
        $this->assertArrayHasKey('id', $responseContent);
        $this->id = $responseContent['id']; // Sauvegarder l'ID pour la modification

        // Recherche d'un caractere
        $response = $this->client->request('GET', "/api/caracters/{$this->id}", [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token,
                'Content-Type' => 'application/json',
            ]
        ]);

        $this->assertEquals(200, $response->getStatusCode()); // Vérifie le code renvoyé

         // Modification du wiki
         $response = $this->client->request('PUT', "/api/caracters/{$this->id}", [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token,
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'name' => 'Khrone Ikels',
                'Content' => 'PALADIN UTLTIME DE LA LUMIERE DIVINE',
            ]
        ]);

        $this->assertEquals(200, $response->getStatusCode()); // Vérifie le code renvoyé

        // Vérifie la modification des valeur
        $responseContent = json_decode($response->getBody(), true);
        $this->assertArrayHasKey('Content', $responseContent);
        $this->assertEquals('PALADIN UTLTIME DE LA LUMIERE DIVINE', $responseContent['Content']);
        
        // Suppression du wiki
        $response = $this->client->request('DELETE', "/api/caracters/{$this->id}", [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token,
                'Content-Type' => 'application/json',
            ]
        ]);

        $this->assertEquals(204, $response->getStatusCode()); // Vérifie le code renvoyé

    }
}
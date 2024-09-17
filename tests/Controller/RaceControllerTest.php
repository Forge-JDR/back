<?php  

namespace App\Tests;

use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;

class RaceControllerTest extends TestCase
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

    public function testCreateUpdateDeleteRace()
    {

        // Creation d'un race
        $response = $this->client->request('POST', '/api/wikis/15/races', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token,
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'name' => 'nom de la race',
                'content' => 'mon histoire',
                'imageUrl' => 'superLien',
            ]
        ]);

        $this->assertEquals(200, $response->getStatusCode()); // Vérifie le code renvoyé

        // Récupère l'ID
        $responseContent = json_decode($response->getBody(), true);
        $race = $responseContent['Races'];
        usort($race, function($a, $b) {
            return strtotime($b['createdAt']) - strtotime($a['createdAt']);
        });
        $dernierRaceCree = $race[0];
        $this->id = $dernierRaceCree['id'];


        // Modification du race
        $response = $this->client->request('PUT', "/api/wikis/15/races/{$this->id}", [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token,
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'name' => 'nom de la race changé',
                'content' => 'mon histoire modifié',
                'imageUrl' => 'superLien',
            ]
        ]);

        $this->assertEquals(200, $response->getStatusCode()); // Vérifie le code renvoyé

        // Récupère le nom
        $responseContent = json_decode($response->getBody(), true);
        $race = $responseContent['Races'];
        usort($race, function($a, $b) {
            return strtotime($b['createdAt']) - strtotime($a['createdAt']);
        });        
        $dernierRaceCree = $race[0];
        $name = $dernierRaceCree['Name'];
        // Vérifie la modification des valeur
        $this->assertEquals('nom de la race changé', $name);
        


        // Suppression du race
        $response = $this->client->request('DELETE', "/api/wikis/15/races/{$this->id}", [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token,
                'Content-Type' => 'application/json',
            ]
        ]);

        $this->assertEquals(200, $response->getStatusCode()); // Vérifie le code renvoyé
    }
}
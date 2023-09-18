<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class RandomUserService
{
    /**
     * Fetch random user data from the API.
     *
     * @param int $count
     * @return array
     */
    public function fetchRandomUsers($count)
    {
        $responses = [];

        for ($i = 0; $i < $count; $i++) {
            $response = Http::get('https://randomuser.me/api/');
            $userData = $response->json()['results'][0];
            $responses[] = [
                'name' => $userData['name']['title'] . ' ' . $userData['name']['first'] . ' ' . $userData['name']['last'],
                'phone' => $userData['phone'],
                'email' => $userData['email'],
                'country' => $userData['location']['country'],
            ];
        }

        return $responses;
    }
    /**
     * Sort users by last name.
     *
     * @param array $users
     * @return array
     */
    public function sortUsersByLastName($users)
    {
        usort($users, function ($a, $b) {
            return strcmp(strrev(explode(' ', $a['name'])[0]), strrev(explode(' ', $b['name'])[0]));
        });

        return $users;
    }
    /**
     * Convert data to XML format.
     *
     * @param array $data
     * @return string
     */
    public function convertToXML($data)
    {
        $xml = new \SimpleXMLElement('<users></users>');

        foreach ($data as $user) {
            $userElement = $xml->addChild('user');
            $userElement->addChild('name', $user['name']);
            $userElement->addChild('phone', $user['phone']);
            $userElement->addChild('email', $user['email']);
            $userElement->addChild('country', $user['country']);
        }

        return $xml->asXML();
    }
}
?>

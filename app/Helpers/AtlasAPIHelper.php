<?php

namespace App\Helpers;

class AtlasAPIHelper
{
    // Atlas API url constants.
    const ATLAS_TEST_API = 'https://gateway-test.atlaspay.online/';
    const ATLAS_LIVE_API = '';

    /**
     * Function Case: Get data from the API.
     * @return string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function GetApi($url, $body = null)
    {
        // Get the header information from the client ID.
        $headers = self::AtlasClientHeaders();

        // Query the API with the header and ther url.
        $client = new \GuzzleHttp\Client(['headers' => $headers]);
        $request = $client->get(self::getAtlasApi() . $url, $body);

        if ($response = $request->getStatusCode() !== 200) {
            dd('Something has gone wrong.');
        }

        // Get the content.
        $response = $request->getBody()->getContents();

        // Return the data.
        return $response;
    }

    /**
     * Function Case: Post data to the API.
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function PostApi($url, $body)
    {
        // Get the header information from the client ID.
        $headers = self::AtlasClientHeaders();

        $client = new \GuzzleHttp\Client(['headers' => $headers]);

        $response = $client->request("POST", self::getAtlasApi() . $url, $body);

        return $response;
    }

    /**
     * Function Case: Update data through the APi.
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function PutApi($url, $body)
    {
        // Get the header information from the client ID.
        $headers = self::AtlasClientHeaders();

        $client = new \GuzzleHttp\Client(['headers' => $headers]);

        $response = $client->request("PUT", self::getAtlasApi() . $url, $body);

        return $response;
    }

    /**
     * Function Case: Atlas header details.
     * @return  array
     */
    private static function AtlasClientHeaders()
    {
        // Return the client information.
        return [
            'userName' => '',
            'password' => '',
            'Content-Type' => 'application/json'
        ];

    }

    /**
     * Function Case: Set the API link.
     * @return string
     */
    private static function getAtlasApi()
    {
        if (config('app.env') === 'production') {
            return self::ATLAS_LIVE_API;
        }

        return self::ATLAS_TEST_API;
    }
}

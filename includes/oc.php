<?php
use GuzzleHttp\Client;
class OcApi
{
    private Client $http;
    private string $base;

    public function __construct()
    {
        // must be set in .env (no default, fail fast)
        $this->base = $_ENV['OPENCENSOR_URL'] ?? '';
        if ($this->base === '') {
            throw new RuntimeException('OPENCENSOR_URL missing in .env');
        }

        // disable SSL verification in dev environment
        // enable SSL verification in production for security
        $this->http = new Client(['verify' => false, 'timeout' => 25]);
    }

    // predict(): classify one text
    public function predict(string $text): array
    {
        $body = ['text' => $text];

        // send text to server and get answer
        $answer = $this->http->post("$this->base/predict", ['json' => $body]);
        // turn answer into array we can use
        $result = json_decode((string)$answer->getBody(), true) ?? [];

        // check if we got both prob and label back
        if (isset($result['prob'], $result['label'])) {
            // make a simple array with two things:
            // 1. the number showing how bad the text is
            // 2. either "PROFANE" if bad or "CLEAN" if good
            return [
                'data' => [
                    (float)$result['prob'], // turn the prob into a number
                    $result['label'] ? 'PROFANE' : 'CLEAN' // if label is true say PROFANE else say CLEAN
                ]
            ];
        }
        // if we didn't get prob and label just send back what we got
        // example: {"error": "Invalid input"}
        return $result;
    }

     // batch(): classify many texts at once
    public function batch(array $texts): string
    {
        $body = ['texts' => $texts];

        // send texts to server
        $answer = $this->http->post("$this->base/batch", ['json' => $body]);
        // send back what server says
        return (string)$answer->getBody();
    }
}

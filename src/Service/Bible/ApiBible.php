<?php

namespace App\Service\Bible;

use App\Entity\BibleQuote;
use App\Repository\BibleQuoteRepository;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ApiBible
{
    private HttpClientInterface $client;
    private ContainerBagInterface $container;
    private string $apiLink;

    public function __construct(HttpClientInterface $client, ContainerBagInterface $container)
    {
        $this->container = $container;
        $this->client = $client;
    }

    public function createChapterVerseLink(string $bible_id, string $chapter_verse): void
    {
        $this->apiLink = 'https://api.scripture.api.bible/v1/bibles/' .  $bible_id . '/verses/' . $chapter_verse;
    }

    public function call(): ResultBibleApi
    {
        if ($this->apiLink == '') return new ResultBibleApi('', 'Api link wasn\'t set', false);
        $response = $this->client->request(
            'GET',
            $this->apiLink,
            [
                'headers' => [
                    'api-key' => $this->getApiKey(),
                ],
            ]
        );

        if ($response->getStatusCode() == 200) {
            $data = $response->toArray();
            if ($data['data']['content']) {
                return new ResultBibleApi($data['data']['content'], '', true);
            } else {
                return new ResultBibleApi('', 'No Content', false);
            }
        } else {
            // this should more specific
            return new ResultBibleApi('', 'I can\'t reach API.BIBLE', false);
        }
    }

    public function getApiKey(): string
    {
        return $this->container->get('app.bible_api_key');
    }
}

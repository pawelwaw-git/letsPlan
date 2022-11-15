<?php

namespace App\Service;

use App\Entity\BibleQuote;
use App\Repository\BibleQuoteRepository;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ApiBible
{
    private HttpClientInterface $client;
    private ContainerBagInterface $container;


    private string $bibleId = '55212e3cf5d04d49-01';
    // private string $bibleId2 = '9879dbb7cfe39e4d-02'; 
    private string $bibleId2 = '7142879509583d59-02';

    private string $verseId = 'SIR.1.23';

    protected BibleQuoteRepository $BibleQuoteRepository;

    public function __construct(HttpClientInterface $client, ContainerBagInterface $container, BibleQuoteRepository $BibleQuoteRepository)
    {
        $this->container = $container;
        $this->client = $client;
        $this->BibleQuoteRepository = $BibleQuoteRepository;
    }

    private function getRandomBibleVerse(): string
    {
        $verses = $this->getBibleVerses();
        return $verses[array_rand($verses, 1)];
    }

    private function getRandomBibleVersion(): string
    {
        $versions = $this->getBibleVersions();
        return $versions[array_rand($versions, 1)];
    }

    private function getBibleVersions()
    {
        return [
            'Cambridge Paragraph Bible of the KJV' => '55212e3cf5d04d49-01',
            'World English Bible' => '9879dbb7cfe39e4d-02',
            'World English Bible British Edition' => '7142879509583d59-02',
        ];
    }

    private function getBibleVerses()
    {
        return [
            'SIR.1.23',
            // 'SIR.14.20',
            // MT.7.12 
            // LUK, 8.5 
        ];
    }

    public function getVerseOfBibleFromApi()
    {
        //check if quote in db then if not then call Api.

        // // Api test later
        $bibleVersion = $this->getRandomBibleVersion();
        $bibleName = array_flip($this->getBibleVersions())[$bibleVersion];
        $bibleChapter = $this->getRandomBibleVerse();
        $bibleEntity = $this->BibleQuoteRepository->findByBibleAndChapter($bibleVersion, $bibleChapter);
        $text = '';
        // dd($bibleEntity);
        // why this is duplicate entry failure
        if ($bibleEntity) {
            $text = $bibleEntity->getHtml(); 
        } else {
            dump($bibleEntity);
            $apiLink = 'https://api.scripture.api.bible/v1/bibles/' . $bibleVersion . '/verses/' . $bibleChapter;
            $response = $this->client->request(
                'GET',
                $apiLink,
                [
                    'headers' => [
                        'api-key' => $this->getApiKey(),
                    ],
                ]
            );

            // such ugly code !!!!! BLEEE
            if ($response->getStatusCode() == 200) {
                $data = $response->toArray();
                if ($data['data']['content']) {
                    $text .= $data['data']['content'];
                } else {
                    $text .= $this->getFallbackText();
                }
            } else {
                $text .= $this->getFallbackText();
            }
            // save to db
            $bibleQuote = new BibleQuote();
            dump($bibleChapter,$bibleName,$bibleVersion,$bibleQuote);
            $bibleQuote->setChapterVerse($bibleChapter);
            $bibleQuote->setHtml($text);
            $bibleQuote->setIdBible($bibleVersion);
            $this->BibleQuoteRepository->save($bibleQuote,true);

        }

        return '<div>
        <figure id="homepage_blockquote">
        <blockquote class="blockquote text-center">
          ' . $text . '
        </blockquote>
        <figcaption class="blockquote-footer text-center">
            ' . $bibleName . ' - <cite title="Source Title">Sir 1,23</cite>
        </figcaption>
      </figure>
      </div>';
    }

    private function getFallbackText(): string
    {
        return '<p class="q1">
        <span data-number="23" data-sid="SIR 1:23" class="v">23</span>
        A patient <span class="add">man</span> will bear for a time,</p>
        <p data-vid="SIR 1:23" class="q1">And afterward joy shall spring up unto him.</p>';
    }

    public function getApiKey(): string
    {
        return $this->container->get('app.bible_api_key');
    }
}

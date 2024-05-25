<?php

declare(strict_types=1);

namespace App\Service\Bible;

use App\Entity\BibleQuote;
use App\Repository\BibleQuoteRepository;

class RandomBibleText
{
    private string $IdBible;
    private string $BibleName;
    private string $BibleChapterAverse;
    private string $wordOfGod = '';
    private ApiBible $api_bible;
    private ?BibleQuote $cached_obeject = null;
    private BibleQuoteRepository $BibleQuoteRepository;
    private ?ResultBibleApi $result_bible_api = null;

    public function __construct(ApiBible $apiBible, BibleQuoteRepository $BibleQuoteRepository)
    {
        $this->IdBible = $this->getRandomBibleId();
        $this->BibleName = $this->getBibleNameBasedOnId($this->IdBible);
        $this->BibleChapterAverse = $this->getRandomBibleVerseNumber();
        $this->api_bible = $apiBible;
        $this->BibleQuoteRepository = $BibleQuoteRepository;
    }

    public function getRandomBibleVerse(): string
    {
        // TODO check if quote in db then if not then call Api. - max is 5000 per day.
        if ($this->isCached()) {
            $this->wordOfGod = $this->cached_obeject->getHtml();
        } else {
            $this->api_bible->createChapterVerseLink($this->getId(), $this->getChapterVerse());
            $this->result_bible_api = $this->api_bible->call();
            if ($this->result_bible_api->isSuccessful()) {
                $this->wordOfGod = $this->result_bible_api->getContent();
                $this->saveToCache();
            } else {
                $this->wordOfGod = $this->getFallbackText();
            }
        }

        return $this->createQuote();
    }

    public function getId(): string
    {
        return $this->IdBible;
    }

    public function getName(): string
    {
        return $this->BibleName;
    }

    public function getChapterVerse(): string
    {
        return $this->BibleChapterAverse;
    }

    private function isCached(): bool
    {
        $bibleEntity = $this->BibleQuoteRepository->findByBibleAndChapter($this);
        if ($bibleEntity instanceof BibleQuote) {
            $this->cached_obeject = $bibleEntity;

            return true;
        }

        return false;
    }

    private function saveToCache(): void
    {
        $bibleQuote = new BibleQuote();
        $bibleQuote->setChapterVerse($this->getChapterVerse());
        $bibleQuote->setHtml($this->wordOfGod);
        $bibleQuote->setIdBible($this->getId());
        $this->BibleQuoteRepository->save($bibleQuote, true);
    }

    private function createQuote(): string
    {
        return '<div>
        <figure id="homepage_blockquote">
        <blockquote class="blockquote text-center">
          '.$this->wordOfGod.'
        </blockquote>
        <figcaption class="blockquote-footer text-center">
            '.$this->getName().' - <cite title="Source Title">'.$this->getChapterVerse().'</cite>
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

    private function getRandomBibleId(): string
    {
        $ids = $this->getBibleVersions();

        return $ids[array_rand($ids, 1)];
    }

    /**
     * @return array<string>
     */
    private function getBibleVersions(): array
    {
        return [
            'Cambridge Paragraph Bible of the KJV' => '55212e3cf5d04d49-01',
            'World English Bible' => '9879dbb7cfe39e4d-02',
            'World English Bible British Edition' => '7142879509583d59-02',
        ];
    }

    private function getBibleNameBasedOnId(string $IdBible): string
    {
        return array_flip($this->getBibleVersions())[$IdBible];
    }

    private function getRandomBibleVerseNumber(): string
    {
        $verses = $this->getBibleVerses();

        return $verses[array_rand($verses, 1)];
    }

    /**
     * @return array<string>
     */
    private function getBibleVerses(): array
    {
        return [
            'SIR.1.23',
            'SIR.14.20',
            // MT.7.12
            // LUK, 8.5
        ];
    }
}

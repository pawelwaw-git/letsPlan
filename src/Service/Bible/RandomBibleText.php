<?php

namespace App\Service\Bible;

use App\Entity\BibleQuote;
use App\Repository\BibleQuoteRepository;

class RandomBibleText
{
    private string $id_bible;
    private string $bible_name;
    private string $bible_chapter_verse;
    private string $word_of_god = '';
    private ApiBible $api_bible;
    private ?BibleQuote $cached_obeject = null;
    private BibleQuoteRepository $BibleQuoteRepository;
    private ?ResultBibleApi $result_bible_api = null;


    public function __construct(ApiBible $apiBible, BibleQuoteRepository $BibleQuoteRepository)
    {
        $this->id_bible = $this->getRandomBibleId();
        $this->bible_name = $this->getBibleNameBasedOnId($this->id_bible);
        $this->bible_chapter_verse = $this->getRandomBibleVerseNumber();
        $this->api_bible = $apiBible;
        $this->BibleQuoteRepository = $BibleQuoteRepository;
    }

    public function getRandomBibleVerse()
    {
        //@todo check if quote in db then if not then call Api. - max is 5000 per day.
        if ($this->isCached()) {
            $this->word_of_god = $this->cached_obeject->getHtml();
        } else {
            $this->api_bible->createChapterVerseLink($this->getId(), $this->getChapterVerse());
            $this->result_bible_api = $this->api_bible->call();
            if ($this->result_bible_api->isSuccefull()) {
                $this->word_of_god = $this->result_bible_api->getContent();
                $this->saveToCache();
            } else {
                $this->word_of_god = $this->getFallbackText();
            }
        }

        return $this->createQuote();
    }

    private function isCached(): bool
    {
        $bibleEntity = $this->BibleQuoteRepository->findByBibleAndChapter($this);
        if ($bibleEntity) {
            $this->cached_obeject = $bibleEntity;
            return true;
        }
        return false;
    }

    private function saveToCache(): void
    {
        $bibleQuote = new BibleQuote();
        $bibleQuote->setChapterVerse($this->getChapterVerse());
        $bibleQuote->setHtml($this->word_of_god);
        $bibleQuote->setIdBible($this->getId());
        $this->BibleQuoteRepository->save($bibleQuote, true);
    }

    private function createQuote(): string
    {
        return '<div>
        <figure id="homepage_blockquote">
        <blockquote class="blockquote text-center">
          ' . $this->word_of_god . '
        </blockquote>
        <figcaption class="blockquote-footer text-center">
            ' . $this->getName() . ' - <cite title="Source Title">' . $this->getChapterVerse() . '</cite>
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

    private function getBibleVersions(): array
    {
        return [
            'Cambridge Paragraph Bible of the KJV' => '55212e3cf5d04d49-01',
            'World English Bible' => '9879dbb7cfe39e4d-02',
            'World English Bible British Edition' => '7142879509583d59-02',
        ];
    }

    private function getBibleNameBasedOnId(string $id_bible): string
    {
        return array_flip($this->getBibleVersions())[$id_bible];
    }

    private function getRandomBibleVerseNumber(): string
    {
        $verses = $this->getBibleVerses();
        return $verses[array_rand($verses, 1)];
    }

    private function getBibleVerses()
    {
        return [
            'SIR.1.23',
            'SIR.14.20',
            // MT.7.12 
            // LUK, 8.5 
        ];
    }

    public function getId(): string
    {
        return $this->id_bible;
    }

    public function getName(): string
    {
        return $this->bible_name;
    }

    public function getChapterVerse(): string
    {
        return $this->bible_chapter_verse;
    }
}

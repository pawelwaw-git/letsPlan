<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\BibleQuoteRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BibleQuoteRepository::class)]
#[ORM\UniqueConstraint(
    name: 'bible_quote_unique_idx',
    columns: ['id_bible', 'chapter_verse']
)]
class BibleQuote
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $IdBible = null;

    #[ORM\Column(length: 255)]
    private ?string $ChapterVerse = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $Html = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdBible(): ?string
    {
        return $this->IdBible;
    }

    public function setIdBible(string $IdBible): self
    {
        $this->IdBible = $IdBible;

        return $this;
    }

    public function getChapterVerse(): ?string
    {
        return $this->ChapterVerse;
    }

    public function setChapterVerse(string $ChapterVerse): self
    {
        $this->ChapterVerse = $ChapterVerse;

        return $this;
    }

    public function getHtml(): ?string
    {
        return $this->Html;
    }

    public function setHtml(string $Html): self
    {
        $this->Html = $Html;

        return $this;
    }
}

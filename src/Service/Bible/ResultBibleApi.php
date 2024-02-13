<?php

namespace App\Service\Bible;

use App\Entity\BibleQuote;
use App\Repository\BibleQuoteRepository;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ResultBibleApi
{
    private string $content;
    private string $errorMessage;
    private bool $success = false;

    public function __construct(string $content, string $errorMessage, bool $success)
    {
        $this->content = $content;
        $this->errorMessage = $errorMessage;
        $this->success = $success;
    }

    public function getErrorMessage():string {
        return $this->errorMessage;
    }
    public function getContent():string {
        return $this->content;
    }

    public function isSuccessful():bool {
        return $this->success;
    }

   
    
}

<?php

declare(strict_types=1);

namespace App\Service\Bible;

class ResultBibleApi
{
    private string $content;
    private string $errorMessage;
    private bool $success = false;

    public function __construct($content, $errorMessage, $success)
    {
        $this->content = $content;
        $this->errorMessage = $errorMessage;
        $this->success = $success;
    }

    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function isSuccefull()
    {
        return $this->success;
    }
}

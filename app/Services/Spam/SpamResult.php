<?php

namespace App\Services\Spam;

readonly class SpamResult
{
    public function __construct(
        public int $score,
        public bool $isSpam,
        public bool $isPending,
        public array $reasons = [],
    ) {}

    public function shouldReject(): bool
    {
        return $this->isSpam;
    }

    public function shouldModerate(): bool
    {
        return $this->isPending;
    }

    public function isClean(): bool
    {
        return ! $this->isSpam && ! $this->isPending;
    }
}

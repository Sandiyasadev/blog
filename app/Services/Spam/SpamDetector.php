<?php

namespace App\Services\Spam;

class SpamDetector
{
    /**
     * Common spam keywords (lowercase).
     */
    private array $spamKeywords = [
        'viagra',
        'cialis',
        'casino',
        'poker',
        'lottery',
        'jackpot',
        'free money',
        'click here',
        'buy now',
        'limited time',
        'act now',
        'cheap pills',
        'weight loss',
        'earn money',
        'work from home',
        'make money fast',
        'double your',
        'nigerian prince',
        'cryptocurrency investment',
        'crypto trading',
        'binary options',
        'forex trading',
        'payday loan',
        'cheap insurance',
        'adult content',
        'xxx',
        'porn',
        'sex toys',
    ];

    /**
     * Maximum allowed links in a comment.
     */
    private int $maxLinks = 2;

    /**
     * Maximum allowed repetitive characters.
     */
    private int $maxRepetitiveChars = 5;

    /**
     * Check if text is spam and return spam score.
     * Score >= 100 means definite spam.
     */
    public function analyze(string $text, ?string $authorName = null, ?string $authorUrl = null): SpamResult
    {
        $score = 0;
        $reasons = [];

        // Check for spam keywords
        $keywordScore = $this->checkKeywords($text);
        if ($keywordScore > 0) {
            $score += $keywordScore;
            $reasons[] = 'Contains spam keywords';
        }

        // Check for excessive links
        $linkScore = $this->checkLinks($text);
        if ($linkScore > 0) {
            $score += $linkScore;
            $reasons[] = 'Too many links';
        }

        // Check for repetitive characters (e.g., "aaaaaa" or "!!!!!!")
        if ($this->hasRepetitiveChars($text)) {
            $score += 20;
            $reasons[] = 'Repetitive characters detected';
        }

        // Check for all caps
        if ($this->isAllCaps($text)) {
            $score += 15;
            $reasons[] = 'All caps text';
        }

        // Check author name for spam patterns
        if ($authorName) {
            $nameScore = $this->checkAuthorName($authorName);
            if ($nameScore > 0) {
                $score += $nameScore;
                $reasons[] = 'Suspicious author name';
            }
        }

        // Check author URL
        if ($authorUrl) {
            $urlScore = $this->checkAuthorUrl($authorUrl);
            if ($urlScore > 0) {
                $score += $urlScore;
                $reasons[] = 'Suspicious author URL';
            }
        }

        // Check for very short meaningless content
        if ($this->isMeaningless($text)) {
            $score += 30;
            $reasons[] = 'Meaningless content';
        }

        return new SpamResult(
            score: $score,
            isSpam: $score >= 100,
            isPending: $score >= 50 && $score < 100,
            reasons: $reasons
        );
    }

    private function checkKeywords(string $text): int
    {
        $text = strtolower($text);
        $score = 0;
        $foundCount = 0;

        foreach ($this->spamKeywords as $keyword) {
            if (str_contains($text, $keyword)) {
                $foundCount++;
                $score += 25;
            }
        }

        // Exponential penalty for multiple spam keywords
        if ($foundCount >= 3) {
            $score += 50;
        }

        return $score;
    }

    private function checkLinks(string $text): int
    {
        // Count URLs in text
        $urlPattern = '/https?:\/\/[^\s]+/i';
        preg_match_all($urlPattern, $text, $matches);
        $linkCount = count($matches[0]);

        if ($linkCount > $this->maxLinks) {
            return 30 + (($linkCount - $this->maxLinks) * 15);
        }

        return 0;
    }

    private function hasRepetitiveChars(string $text): bool
    {
        // Check for same character repeated more than allowed
        $pattern = '/(.)\1{' . $this->maxRepetitiveChars . ',}/u';

        return (bool) preg_match($pattern, $text);
    }

    private function isAllCaps(string $text): bool
    {
        // Remove non-letter characters
        $letters = preg_replace('/[^a-zA-Z]/u', '', $text);

        if (strlen($letters) < 10) {
            return false;
        }

        $uppercase = preg_replace('/[^A-Z]/', '', $letters);

        return strlen($uppercase) / strlen($letters) > 0.8;
    }

    private function checkAuthorName(string $name): int
    {
        $score = 0;

        // Name contains URL
        if (preg_match('/https?:\/\//i', $name)) {
            $score += 50;
        }

        // Name is too long (likely spam)
        if (strlen($name) > 50) {
            $score += 20;
        }

        // Name contains spam keywords
        $score += $this->checkKeywords($name);

        return $score;
    }

    private function checkAuthorUrl(string $url): int
    {
        $score = 0;

        // Check if URL contains spam keywords
        $urlLower = strtolower($url);
        foreach ($this->spamKeywords as $keyword) {
            if (str_contains($urlLower, str_replace(' ', '', $keyword))) {
                $score += 30;
                break;
            }
        }

        return $score;
    }

    private function isMeaningless(string $text): bool
    {
        // Remove whitespace
        $cleaned = preg_replace('/\s+/', '', $text);

        // Too short
        if (strlen($cleaned) < 3) {
            return true;
        }

        // Just numbers or symbols
        $letters = preg_replace('/[^a-zA-Z\p{L}]/u', '', $text);
        if (strlen($letters) < 2) {
            return true;
        }

        return false;
    }
}

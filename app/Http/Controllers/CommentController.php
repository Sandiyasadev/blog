<?php

namespace App\Http\Controllers;

use App\Enums\CommentStatus;
use App\Http\Requests\StoreCommentRequest;
use App\Models\Comment;
use App\Models\Post;
use App\Services\Spam\SpamDetector;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class CommentController extends Controller
{
    public function __construct(
        private readonly SpamDetector $spamDetector
    ) {}

    public function store(StoreCommentRequest $request, Post $post)
    {
        if (! $post->allow_comments) {
            throw ValidationException::withMessages([
                'body_raw' => 'Komentar sedang dinonaktifkan.',
            ]);
        }

        // Honeypot check (always enabled - very lightweight)
        if ($request->filled('website')) {
            throw ValidationException::withMessages([
                'body_raw' => 'Komentar terdeteksi spam.',
            ]);
        }

        // Rate limiting (can be disabled)
        if (config('blog.antispam.rate_limit_enabled', true)) {
            $rateKey = sprintf('comment:%s', $request->ip());
            if (RateLimiter::tooManyAttempts($rateKey, 5)) {
                throw ValidationException::withMessages([
                    'body_raw' => 'Terlalu banyak komentar. Coba lagi nanti.',
                ]);
            }
            RateLimiter::hit($rateKey, 60);
        }

        // Parent comment validation
        $parentId = $request->input('parent_id');
        if ($parentId) {
            $parentExists = Comment::query()
                ->where('id', $parentId)
                ->where('post_id', $post->id)
                ->exists();

            if (! $parentExists) {
                throw ValidationException::withMessages([
                    'parent_id' => 'Balasan tidak valid.',
                ]);
            }
        }

        $bodyRaw = $request->string('body_raw')->toString();
        $bodySanitized = trim(strip_tags($bodyRaw));
        $authorName = $request->string('author_name')->toString();
        $authorUrl = $request->string('author_url')->toString() ?: null;

        $spamScore = 0;
        $status = CommentStatus::Pending;

        // Spam detection (can be disabled)
        if (config('blog.antispam.spam_detection_enabled', true)) {
            $spamResult = $this->spamDetector->analyze($bodySanitized, $authorName, $authorUrl);

            if ($spamResult->shouldReject()) {
                throw ValidationException::withMessages([
                    'body_raw' => 'Komentar terdeteksi sebagai spam.',
                ]);
            }

            $spamScore = $spamResult->score;

            // Auto-approve if spam detection is enabled and comment is clean
            if (config('blog.antispam.auto_approve_clean', false) && $spamResult->isClean()) {
                $status = CommentStatus::Approved;
            }
        } else {
            // If spam detection is disabled, optionally auto-approve all comments
            if (config('blog.antispam.auto_approve_when_disabled', false)) {
                $status = CommentStatus::Approved;
            }
        }

        Comment::create([
            'post_id' => $post->id,
            'parent_id' => $parentId,
            'author_name' => $authorName,
            'author_email' => $request->string('author_email')->toString() ?: null,
            'author_url' => $authorUrl,
            'body_raw' => $bodyRaw,
            'body_sanitized' => $bodySanitized,
            'status' => $status,
            'ip_address' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 512),
            'spam_score' => $spamScore,
        ]);

        $message = $status === CommentStatus::Approved
            ? 'Komentar berhasil dikirim.'
            : 'Komentar terkirim dan menunggu moderasi.';

        return back()->with('status', $message);
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactRequest;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class PageController extends Controller
{
    public function about()
    {
        $author = User::query()->first();

        return view('pages.about', [
            'author' => $author,
        ]);
    }

    public function contact()
    {
        return view('pages.contact');
    }

    public function sendContact(ContactRequest $request)
    {
        // Honeypot check
        if ($request->filled('website')) {
            throw ValidationException::withMessages([
                'message' => 'Pesan terdeteksi sebagai spam.',
            ]);
        }

        // Rate limiting
        $rateKey = sprintf('contact:%s', $request->ip());
        if (RateLimiter::tooManyAttempts($rateKey, 3)) {
            throw ValidationException::withMessages([
                'message' => 'Terlalu banyak pesan. Coba lagi nanti.',
            ]);
        }
        RateLimiter::hit($rateKey, 300); // 5 menit cooldown

        // Log the contact message (untuk MVP, simpan ke log dulu)
        Log::channel('single')->info('Contact form submission', [
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'subject' => $request->input('subject'),
            'message' => $request->input('message'),
            'ip' => $request->ip(),
        ]);

        // TODO: Uncomment ini jika MAIL sudah dikonfigurasi
        // $admin = User::query()->first();
        // if ($admin) {
        //     Mail::to($admin->email)->send(new \App\Mail\ContactFormMail(
        //         $request->input('name'),
        //         $request->input('email'),
        //         $request->input('subject'),
        //         $request->input('message'),
        //     ));
        // }

        return back()->with('success', 'Terima kasih! Pesan Anda telah terkirim.');
    }
}

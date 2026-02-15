<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Jobs\ParsePageJob;
use App\Models\Settings;
use App\Models\Review;
use Illuminate\Validation\ValidationException;

class AdminController extends Controller
{
    public function enter()
    {
        if (Auth::check()) 
            return redirect()->route('admin.settings');
        
        return Inertia::render('Admin/Enter');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember')))
        {
            $request->session()->regenerate();
            return redirect()->intended(route('admin.settings'));
        }

        throw ValidationException::withMessages([
            'email' => __('auth.failed'),
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('enter');
    }

    public function reviews(Request $request) 
    {
        $validSorts = ['rating_asc', 'rating_desc', 'date_asc', 'date_desc'];
        $sort = $request->get('sort', 'date_desc'); // по умолчанию новые
        
        if (!in_array($sort, $validSorts)) {
            $sort = 'date_desc';
        }

        $query = Review::query();

        // Применяем сортировку
        switch ($sort) {
            case 'rating_asc':
                $query->orderBy('rating', 'asc')
                      ->orderBy('review_created_at', 'desc');
                break;
            case 'rating_desc':
                $query->orderBy('rating', 'desc')
                      ->orderBy('review_created_at', 'desc');
                break;
            case 'date_asc':
                $query->orderBy('review_created_at', 'asc');
                break;
            case 'date_desc':
                $query->orderBy('review_created_at', 'desc');
                break;
        }

        $reviews = $query->paginate(20)->withQueryString();
        
        $settings = Settings::getSettings();
        
        // Считаем статистику
        $stats = [
            'rating' => $settings->rating,
            'reviews_count' => $settings->reviews_count ?? 0,
            'progress' => $settings->progress
        ];

        return Inertia::render('Admin/Reviews', [
            'reviews' => $reviews,
            'stats' => $stats,
            'filters' => [
                'sort' => $sort,
            ],
            'currentPath' => $request->path(), 
            'admin' => auth()->user(),
        ]);
    }
    public function settings(Request $request) 
    {
        $settings = Settings::getSettings();

        return Inertia::render('Admin/Settings', [
            'admin' => auth()->user(),
            'currentPath' => $request->path(),
            'settings' => [
                'org_link' => $settings->org_link,
                'progress' => $settings->progress

            ]
        ]);
    }

public function update(Request $request)
{
    $request->validate([
        'org_link' => 'nullable|url',
    ]);

    $settings = Settings::getSettings();
    $oldLink = $settings->org_link;
    $newLink = $request->org_link;

    // Обновляем ссылку
    $settings->update([
        'org_link' => $newLink ?? $oldLink,
    ]);

    // ЕСЛИ ССЫЛКА ИЗМЕНИЛАСЬ
    if ($newLink && $oldLink !== $newLink) 
    {
        // 1. Удаляем все отзывы
        Review::truncate(); // Быстро и чисто
        
        // 2. Обнуляем поля в настройках
        $settings->update([
            'rating' => 0.0,
            'progress' => 0,
            'reviews_count' => 0,
        ]);

        ParsePageJob::dispatch($newLink);
        return redirect()->back()->with('success', 'Ссылка изменена, все отзывы удалены');
    }

    return redirect()->back()->with('success', 'Настройки сохранены');
}
}
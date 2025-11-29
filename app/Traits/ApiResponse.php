<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

trait ApiResponse
{
    /**
     * Return a response that works for both API and Web requests
     *
     * @param mixed $data
     * @param string|null $view
     * @param array $viewData
     * @param int $status
     * @return JsonResponse|View|RedirectResponse
     */
    protected function respond($data = null, ?string $view = null, array $viewData = [], int $status = 200)
    {
        // Check if request expects JSON (API request) or is AJAX
        if (request()->expectsJson() || request()->is('api/*') || request()->ajax()) {
            return response()->json([
                'success' => true,
                'data' => $data,
                'message' => $data['message'] ?? null,
            ], $status);
        }

        // For web requests, return view or redirect
        if ($view) {
            return view($view, array_merge($viewData, ['data' => $data]));
        }

        // If redirect is in data, return redirect
        if (isset($data['redirect'])) {
            return redirect($data['redirect'])
                ->with('success', $data['message'] ?? 'Operation successful');
        }

        // Default: return back with message
        return back()->with('success', $data['message'] ?? 'Operation successful');
    }

    /**
     * Return error response for both API and Web
     *
     * @param string $message
     * @param int $status
     * @param array $errors
     * @return JsonResponse|RedirectResponse
     */
    protected function respondError(string $message, int $status = 400, array $errors = [])
    {
        if (request()->expectsJson() || request()->is('api/*') || request()->ajax()) {
            return response()->json([
                'success' => false,
                'message' => $message,
                'errors' => $errors,
            ], $status);
        }

        return back()
            ->withInput()
            ->withErrors($errors)
            ->with('error', $message);
    }
}


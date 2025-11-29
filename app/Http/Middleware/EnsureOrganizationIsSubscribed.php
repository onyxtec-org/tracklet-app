<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureOrganizationIsSubscribed
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Super admin bypasses subscription check
        if ($user && $user->isSuperAdmin()) {
            return $next($request);
        }

        // Check if user belongs to an organization
        if (!$user || !$user->organization) {
            if ($request->expectsJson() || $request->is('api/*') || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You must belong to an organization.',
                ], 403);
            }
            return redirect()->route('login');
        }

        // Check if organization is subscribed
        if (!$user->organization->isSubscribed()) {
            if ($request->expectsJson() || $request->is('api/*') || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Your organization subscription has expired. Please renew your subscription.',
                    'redirect' => route('subscription.checkout'),
                ], 402);
            }
            return redirect()->route('subscription.checkout')
                ->with('error', 'Your organization subscription has expired. Please renew your subscription.');
        }

        return $next($request);
    }
}

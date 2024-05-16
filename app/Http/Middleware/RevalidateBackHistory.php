<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RevalidateBackHistory
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if requested route is outside of admin task get task
        /*if (!$request->is('admin-tasks/get-task*') || !$request->is('/admin-home/show-department/assigned-tasks')) {
            $request->session()->forget('taskName');
        }

        // Check if requested route is outside of show department
        if (!$request->is('admin-home/show-department*')) {
            $request->session()->forget('department');
        }*/
        
        // Prevent clicking back then bypassing sessions, etc.
        $response = $next($request);

        $headers = [
            'Cache-Control' => 'no-cache, no-store, max-age=0, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => 'Sat 01 Jan 1990 00:00:00 GMT'
        ];

        foreach ($headers as $key => $value) {
            $response->headers->set($key, $value);
        }

        return $response;
    }
}

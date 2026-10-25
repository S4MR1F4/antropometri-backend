<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class SecurityHardeningMiddleware
{
    /**
     * Known malicious patterns commonly used in PHP-CGI argument injection (CVE-2024-4577),
     * php2shell, remote code execution (RCE), and LFI/RFI wrappers.
     */
    private const BLOCKED_PATTERNS = [
        '%ad',                     // Soft hyphen used in CVE-2024-4577
        'allow_url_include',       // PHP ini directive injection
        'auto_prepend_file',       // PHP ini directive injection
        'auto_append_file',        // PHP ini directive injection
        'php://input',             // PHP input stream wrapper
        'php://filter',            // PHP filter wrapper
        'phar://',                 // PHAR deserialization wrapper
        'data://text',             // Data URI execution
        'cgi-bin',                 // CGI directory traversal
        '/etc/passwd',             // LFI indicator
        '..\..\winnt',             // Path traversal Windows
        '..\..\windows',           // Path traversal Windows
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $rawUri = $request->getRequestUri();
        $queryString = $request->getQueryString() ?? '';
        $fullTarget = strtolower($rawUri . ' ' . $queryString);

        // 1. Detect & Block php2shell / CVE-2024-4577 / RCE attack vectors
        foreach (self::BLOCKED_PATTERNS as $pattern) {
            if (str_contains($fullTarget, strtolower($pattern))) {
                Log::warning('SecurityHardening: Blocked malicious request attempt.', [
                    'ip' => $request->ip(),
                    'pattern' => $pattern,
                    'uri' => $rawUri,
                    'user_agent' => $request->userAgent(),
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Permintaan ditolak oleh filter keamanan sistem.',
                ], 403);
            }
        }

        // 2. Prevent null byte injection in input parameters
        foreach ($request->all() as $key => $value) {
            if (is_string($value) && str_contains($value, "\0")) {
                return response()->json([
                    'success' => false,
                    'message' => 'Karakter terlarang terdeteksi pada parameter input.',
                ], 400);
            }
        }

        // 3. Process Request
        $response = $next($request);

        // 4. Inject Strict Security Headers
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'geolocation=(), camera=(), microphone=(), payment=()');

        return $response;
    }
}

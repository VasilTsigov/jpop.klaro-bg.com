<?php

namespace App\Support;

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * A tiny in-process browser for `jpop:selftest`: keeps cookies and the CSRF token, sends requests through the HTTP kernel
 * (routes, middleware, policies as in production) and never follows redirects, so the caller sees them.
 */
class SelftestBrowser
{
    /** @var array<string, string> */
    private array $cookies = [];

    private ?string $token = null;

    public function get(string $path): Response
    {
        return $this->send('GET', $path);
    }

    /**
     * @param  array<string, mixed>  $data  fields; UploadedFile values are sent as files
     */
    public function post(string $path, array $data = []): Response
    {
        return $this->send('POST', $path, $data);
    }

    private function send(string $method, string $path, array $data = []): Response
    {
        $files = [];
        foreach ($data as $key => $value) {
            if ($value instanceof \Illuminate\Http\UploadedFile) {
                $files[$key] = $value;
                unset($data[$key]);
            } elseif (is_array($value) && collect($value)->contains(fn ($v) => $v instanceof \Illuminate\Http\UploadedFile)) {
                $files[$key] = $value;
                unset($data[$key]);
            }
        }
        if ($method === 'POST' && $this->token !== null && ! isset($data['_token'])) {
            $data['_token'] = $this->token;
        }

        $url = str_starts_with($path, 'http') ? $path : rtrim(config('app.url'), '/').'/'.ltrim($path, '/');
        $request = Request::create($url, $method, $data, $this->cookies, $files, ['HTTP_ACCEPT' => 'text/html']);

        // the kernel is reused for every request here, so nothing may leak from the previous user
        app('auth')->forgetGuards();
        app('session.store')->flush();

        $kernel = app(Kernel::class);
        $response = $kernel->handle($request);
        $kernel->terminate($request, $response);

        foreach ($response->headers->getCookies() as $cookie) {
            if ($cookie->isCleared()) {
                unset($this->cookies[$cookie->getName()]);
            } else {
                $this->cookies[$cookie->getName()] = $cookie->getValue();
            }
        }
        if (! $response->isRedirect() && preg_match('/name="_token"\s+value="([^"]+)"/', (string) $response->getContent(), $m)) {
            $this->token = $m[1];
        }

        return $response;
    }
}

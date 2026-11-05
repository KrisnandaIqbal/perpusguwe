<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Mode pemeliharaan: kalau `app.maintenance = true` di .env, semua request
 * dijawab halaman 503. IP di `app.maintenanceAllowIPs` (dipisah koma) tetap
 * bisa masuk, supaya admin bisa mengecek situs sebelum dibuka lagi.
 */
class Maintenance implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (getenv('app.maintenance') !== 'true') {
            return null;
        }

        $allowed = array_filter(array_map('trim', explode(',', (string) getenv('app.maintenanceAllowIPs'))));
        if (in_array($request->getIPAddress(), $allowed, true)) {
            return null;
        }

        return service('response')
            ->setStatusCode(503)
            ->setHeader('Retry-After', '3600')
            ->setBody(view('errors/html/error_503'));
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}

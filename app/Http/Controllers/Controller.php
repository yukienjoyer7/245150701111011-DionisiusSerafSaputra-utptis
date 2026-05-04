<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    title: 'Sistem Absensi API',
    description: 'API untuk sistem absensi mahasiswa/karyawan menggunakan Laravel tanpa database.',
)]
#[OA\Server(
    url: 'http://127.0.0.1:8000',
    description: 'Local development server'
)]
#[OA\SecurityScheme(
    securityScheme: 'bearerAuth',
    type: 'http',
    scheme: 'bearer',
    bearerFormat: 'JWT'
)]
abstract class Controller {}

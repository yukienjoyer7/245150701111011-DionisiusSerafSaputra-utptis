<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use OpenApi\Attributes as OA;

class AttendanceController extends Controller
{
    private static array $attendances = [
        [
            'id' => 1,
            'name' => 'Sakura no Uta',
            'date' => '2026-05-04',
            'status' => 'hadir',
        ],
        [
            'id' => 2,
            'name' => 'Summer Pockets',
            'date' => '2026-05-04',
            'status' => 'alpa',
        ],
    ];

    private static int $nextId = 3;

    private array $allowedStatuses = ['hadir', 'izin', 'sakit', 'alpa'];

    #[OA\Get(
        path: '/api/attendances',
        summary: 'Ambil semua data absensi',
        security: [['bearerAuth' => []]],
        tags: ['Attendance'],
        responses: [
            new OA\Response(response: 200, description: 'Data absensi berhasil diambil'),
            new OA\Response(response: 401, description: 'Unauthenticated'),
        ]
    )]
    public function index()
    {
        return response()->json([
            'message' => 'Data absensi berhasil diambil.',
            'data' => self::$attendances,
        ], 200);
    }

    #[OA\Get(
        path: '/api/attendances/{id}',
        summary: 'Ambil detail absensi berdasarkan ID',
        security: [['bearerAuth' => []]],
        tags: ['Attendance'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer'),
                example: 1
            ),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Data absensi berhasil diambil'),
            new OA\Response(response: 404, description: 'Data absensi tidak ditemukan'),
            new OA\Response(response: 401, description: 'Unauthenticated'),
        ]
    )]
    public function show($id)
    {
        $attendance = collect(self::$attendances)->firstWhere('id', (int) $id);

        if (! $attendance) {
            return response()->json([
                'message' => "Data absensi dengan ID {$id} tidak ditemukan.",
            ], 404);
        }

        return response()->json([
            'message' => 'Data absensi berhasil diambil.',
            'data' => $attendance,
        ], 200);
    }

    #[OA\Post(
        path: '/api/attendances',
        summary: 'Tambah data absensi baru',
        security: [['bearerAuth' => []]],
        tags: ['Attendance'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name', 'date', 'status'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'Sakura no Uta'),
                    new OA\Property(property: 'date', type: 'string', example: '2026-05-04'),
                    new OA\Property(property: 'status', type: 'string', enum: ['hadir', 'izin', 'sakit', 'alpa'], example: 'hadir'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Data absensi berhasil ditambahkan'),
            new OA\Response(response: 422, description: 'Validasi gagal'),
            new OA\Response(response: 401, description: 'Unauthenticated'),
        ]
    )]
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string',
                'date' => 'required|date_format:Y-m-d',
                'status' => 'required|in:hadir,izin,sakit,alpa',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validasi gagal.',
                'errors' => $e->errors(),
            ], 422);
        }

        $attendance = [
            'id' => self::$nextId++,
            'name' => $request->name,
            'date' => $request->date,
            'status' => $request->status,
        ];

        self::$attendances[] = $attendance;

        return response()->json([
            'message' => 'Data absensi berhasil ditambahkan.',
            'data' => $attendance,
        ], 201);
    }

    #[OA\Put(
        path: '/api/attendances/{id}',
        summary: 'Update seluruh data absensi berdasarkan ID',
        security: [['bearerAuth' => []]],
        tags: ['Attendance'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer'),
                example: 1
            ),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name', 'date', 'status'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'Sakura no Uta'),
                    new OA\Property(property: 'date', type: 'string', example: '2026-05-04'),
                    new OA\Property(property: 'status', type: 'string', enum: ['hadir', 'izin', 'sakit', 'alpa'], example: 'izin'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Data absensi berhasil diperbarui'),
            new OA\Response(response: 404, description: 'Data absensi tidak ditemukan'),
            new OA\Response(response: 422, description: 'Validasi gagal'),
            new OA\Response(response: 401, description: 'Unauthenticated'),
        ]
    )]
    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'name' => 'required|string',
                'date' => 'required|date_format:Y-m-d',
                'status' => 'required|in:hadir,izin,sakit,alpa',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validasi gagal.',
                'errors' => $e->errors(),
            ], 422);
        }

        $index = collect(self::$attendances)->search(fn ($a) => $a['id'] === (int) $id);

        if ($index === false) {
            return response()->json([
                'message' => "Data absensi dengan ID {$id} tidak ditemukan.",
            ], 404);
        }

        self::$attendances[$index] = [
            'id' => (int) $id,
            'name' => $request->name,
            'date' => $request->date,
            'status' => $request->status,
        ];

        return response()->json([
            'message' => 'Data absensi berhasil diperbarui.',
            'data' => self::$attendances[$index],
        ], 200);
    }

    #[OA\Patch(
        path: '/api/attendances/{id}',
        summary: 'Update status absensi berdasarkan ID',
        security: [['bearerAuth' => []]],
        tags: ['Attendance'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer'),
                example: 1
            ),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['status'],
                properties: [
                    new OA\Property(property: 'status', type: 'string', enum: ['hadir', 'izin', 'sakit', 'alpa'], example: 'sakit'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Status absensi berhasil diperbarui'),
            new OA\Response(response: 404, description: 'Data absensi tidak ditemukan'),
            new OA\Response(response: 422, description: 'Validasi gagal'),
            new OA\Response(response: 401, description: 'Unauthenticated'),
        ]
    )]
    public function patch(Request $request, $id)
    {
        try {
            $request->validate([
                'status' => 'required|in:hadir,izin,sakit,alpa',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validasi gagal.',
                'errors' => $e->errors(),
            ], 422);
        }

        $index = collect(self::$attendances)->search(fn ($a) => $a['id'] === (int) $id);

        if ($index === false) {
            return response()->json([
                'message' => "Data absensi dengan ID {$id} tidak ditemukan.",
            ], 404);
        }

        self::$attendances[$index]['status'] = $request->status;

        return response()->json([
            'message' => 'Status absensi berhasil diperbarui.',
            'data' => self::$attendances[$index],
        ], 200);
    }

    #[OA\Delete(
        path: '/api/attendances/{id}',
        summary: 'Hapus data absensi berdasarkan ID',
        security: [['bearerAuth' => []]],
        tags: ['Attendance'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer'),
                example: 1
            ),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Data absensi berhasil dihapus'),
            new OA\Response(response: 404, description: 'Data absensi tidak ditemukan'),
            new OA\Response(response: 401, description: 'Unauthenticated'),
        ]
    )]
    public function destroy($id)
    {
        $index = collect(self::$attendances)->search(fn ($a) => $a['id'] === (int) $id);

        if ($index === false) {
            return response()->json([
                'message' => "Data absensi dengan ID {$id} tidak ditemukan.",
            ], 404);
        }

        $deleted = self::$attendances[$index];
        array_splice(self::$attendances, $index, 1);

        return response()->json([
            'message' => 'Data absensi berhasil dihapus.',
            'data' => $deleted,
        ], 200);
    }
}

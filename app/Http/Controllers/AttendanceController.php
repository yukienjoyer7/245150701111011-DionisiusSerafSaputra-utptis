<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

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

    public function index()
    {
        return response()->json([
            'message' => 'Data absensi berhasil diambil.',
            'data' => self::$attendances,
        ], 200);
    }

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

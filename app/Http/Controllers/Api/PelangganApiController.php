<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePelangganRequest;
use App\Http\Requests\UpdatePelangganRequest;
use App\Http\Resources\PelangganResource;
use App\Services\PelangganService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Exception;

class PelangganApiController extends Controller
{
    protected PelangganService $pelangganService;

    /**
     * Dependency Injection PelangganService.
     */
    public function __construct(PelangganService $pelangganService)
    {
        $this->pelangganService = $pelangganService;
    }

    /**
     * [GET /api/pelanggan]
     * Mengambil seluruh data pelanggan dengan opsi filter & pagination.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $params = [
                'search'     => $request->query('search'),
                'status'     => $request->query('status'),
                'sort_by'    => $request->query('sort_by', 'created_at'),
                'sort_order' => $request->query('sort_order', 'desc'),
                'per_page'   => $request->query('per_page'),
            ];

            $data = $this->pelangganService->getAll($params);

            return response()->json([
                'success' => true,
                'message' => 'Data pelanggan berhasil diambil.',
                'data'    => PelangganResource::collection($data),
                'meta'    => method_exists($data, 'total') ? [
                    'current_page' => $data->currentPage(),
                    'last_page'    => $data->lastPage(),
                    'per_page'     => $data->perPage(),
                    'total'        => $data->total(),
                ] : [
                    'total' => $data->count(),
                ],
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data pelanggan: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * [POST /api/pelanggan]
     * Menyimpan data pelanggan baru.
     */
    public function store(StorePelangganRequest $request): JsonResponse
    {
        try {
            $validatedData = $request->validated();
            $pelanggan = $this->pelangganService->create($validatedData);

            return response()->json([
                'success' => true,
                'message' => 'Data pelanggan berhasil ditambahkan.',
                'data'    => new PelangganResource($pelanggan),
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan data pelanggan: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * [GET /api/pelanggan/{id}]
     * Mengambil detail pelanggan berdasarkan ID.
     */
    public function show(int $id): JsonResponse
    {
        try {
            $pelanggan = $this->pelangganService->getById($id);

            if (!$pelanggan) {
                return response()->json([
                    'success' => false,
                    'message' => "Data pelanggan dengan ID {$id} tidak ditemukan.",
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Detail pelanggan berhasil ditemukan.',
                'data'    => new PelangganResource($pelanggan),
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * [PUT/PATCH /api/pelanggan/{id}]
     * Memperbarui data pelanggan.
     */
    public function update(UpdatePelangganRequest $request, int $id): JsonResponse
    {
        try {
            $validatedData = $request->validated();
            $pelanggan = $this->pelangganService->update($id, $validatedData);

            return response()->json([
                'success' => true,
                'message' => 'Data pelanggan berhasil diperbarui.',
                'data'    => new PelangganResource($pelanggan),
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 404);
        }
    }

    /**
     * [DELETE /api/pelanggan/{id}]
     * Menghapus data pelanggan.
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->pelangganService->delete($id);

            return response()->json([
                'success' => true,
                'message' => 'Data pelanggan berhasil dihapus.',
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 404);
        }
    }

    /**
     * [GET /api/pelanggan/stats/summary]
     * Mengambil ringkasan statistik pelanggan.
     */
    public function stats(): JsonResponse
    {
        try {
            $stats = $this->pelangganService->getStats();

            return response()->json([
                'success' => true,
                'message' => 'Statistik pelanggan berhasil dimuat.',
                'data'    => $stats,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat statistik: ' . $e->getMessage(),
            ], 500);
        }
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreKategoriRequest;
use App\Http\Requests\UpdateKategoriRequest;
use App\Http\Resources\KategoriResource;
use App\Services\KategoriService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Exception;

class KategoriApiController extends Controller
{
    protected KategoriService $kategoriService;

    /**
     * Dependency Injection KategoriService.
     */
    public function __construct(KategoriService $kategoriService)
    {
        $this->kategoriService = $kategoriService;
    }

    /**
     * [GET /api/kategori]
     * Mengambil daftar seluruh data kategori dengan opsi pencarian, sorting, dan paginasi.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $params = [
                'search'     => $request->query('search'),
                'sort_by'    => $request->query('sort_by', 'id_kategori'),
                'sort_order' => $request->query('sort_order', 'asc'),
                'per_page'   => $request->query('per_page'),
            ];

            $data = $this->kategoriService->getAll($params);

            return response()->json([
                'success' => true,
                'message' => 'Data kategori berhasil diambil.',
                'data'    => KategoriResource::collection($data),
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
                'message' => 'Gagal mengambil data kategori: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * [POST /api/kategori]
     * Menyimpan data kategori baru.
     */
    public function store(StoreKategoriRequest $request): JsonResponse
    {
        try {
            $validatedData = $request->validated();
            $kategori = $this->kategoriService->create($validatedData);

            return response()->json([
                'success' => true,
                'message' => 'Data kategori berhasil ditambahkan.',
                'data'    => new KategoriResource($kategori),
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan data kategori: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * [GET /api/kategori/{id_kategori}]
     * Mengambil detail kategori berdasarkan ID.
     */
    public function show(int $id_kategori): JsonResponse
    {
        try {
            $kategori = $this->kategoriService->getById($id_kategori);

            if (!$kategori) {
                return response()->json([
                    'success' => false,
                    'message' => "Data kategori dengan ID {$id_kategori} tidak ditemukan.",
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Detail kategori berhasil ditemukan.',
                'data'    => new KategoriResource($kategori),
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * [PUT/PATCH /api/kategori/{id_kategori}]
     * Memperbarui data kategori.
     */
    public function update(UpdateKategoriRequest $request, int $id_kategori): JsonResponse
    {
        try {
            $validatedData = $request->validated();
            $kategori = $this->kategoriService->update($id_kategori, $validatedData);

            return response()->json([
                'success' => true,
                'message' => 'Data kategori berhasil diperbarui.',
                'data'    => new KategoriResource($kategori),
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 404);
        }
    }

    /**
     * [DELETE /api/kategori/{id_kategori}]
     * Menghapus data kategori.
     */
    public function destroy(int $id_kategori): JsonResponse
    {
        try {
            $this->kategoriService->delete($id_kategori);

            return response()->json([
                'success' => true,
                'message' => 'Data kategori berhasil dihapus.',
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 404);
        }
    }
}

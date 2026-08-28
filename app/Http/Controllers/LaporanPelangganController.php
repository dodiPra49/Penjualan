<?php

namespace App\Http\Controllers;

use App\Models\Pelanggan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class LaporanPelangganController extends Controller
{
    /**
     * Menampilkan halaman utama Laporan Pelanggan
     */
    public function index(Request $request)
    {
        $status = $request->input('status', 'semua');
        $search = $request->input('search', '');

        // Query data untuk pratinjau tabel di web
        $query = Pelanggan::query();

        if ($status !== 'semua' && in_array($status, ['aktif', 'nonaktif'])) {
            $query->where('status', $status);
        }

        if (!empty($search)) {
            $query->search($search);
        }

        $pelanggans = $query->orderBy('kode_pelanggan', 'asc')->paginate(15)->withQueryString();

        // Statistik ringkas
        $totalSemua = Pelanggan::count();
        $totalAktif = Pelanggan::where('status', 'aktif')->count();
        $totalNonaktif = Pelanggan::where('status', 'nonaktif')->count();

        return view('laporan.pelanggan.index', compact(
            'pelanggans',
            'status',
            'search',
            'totalSemua',
            'totalAktif',
            'totalNonaktif'
        ));
    }

    /**
     * Memproses JasperReport dan menghasilkan file (PDF, XLSX, CSV)
     *
     * @param string $format 'pdf', 'xlsx', 'csv'
     * @param string $statusFilter 'semua', 'aktif', 'nonaktif'
     * @return string Path file output lengkap
     */
    protected function executeJasperReport($format = 'pdf', $statusFilter = 'semua')
    {
        $inputJrxml = resource_path('reports/laporan_pelanggan.jrxml');
        $outputDir  = storage_path('app/public/reports');

        if (!File::exists($outputDir)) {
            File::makeDirectory($outputDir, 0755, true);
        }

        // 1. Ambil data pelanggan sesuai filter status
        $query = Pelanggan::query();
        if ($statusFilter !== 'semua' && in_array($statusFilter, ['aktif', 'nonaktif'])) {
            $query->where('status', $statusFilter);
        }
        
        $dataPelanggan = $query->orderBy('kode_pelanggan', 'asc')->get()->map(function ($p) {
            return [
                'id'             => $p->id,
                'kode_pelanggan' => $p->kode_pelanggan,
                'nama_pelanggan' => $p->nama_pelanggan,
                'email'          => $p->email ?? '-',
                'nomor_telepon'  => $p->nomor_telepon ?? '-',
                'alamat'         => $p->alamat ?? '-',
                'status'         => $p->status,
            ];
        })->values()->toArray();

        // 2. Simpan data sementara dalam format JSON untuk JasperReports JsonDataSource
        $timestamp      = time() . '_' . mt_rand(1000, 9999);
        $tempJsonFile   = $outputDir . DIRECTORY_SEPARATOR . "data_{$timestamp}.json";
        $outputBasePath = $outputDir . DIRECTORY_SEPARATOR . "laporan_pelanggan_{$timestamp}";

        File::put($tempJsonFile, json_encode($dataPelanggan, JSON_UNESCAPED_UNICODE));

        // 3. Susun classpath Java & command JasperReports Runner
        $reportsPath   = resource_path('reports');
        $libPath       = base_path('vendor/geekcom/phpjasper/bin/jasperstarter/lib/*');
        $jdbcPath      = base_path('vendor/geekcom/phpjasper/bin/jasperstarter/jdbc/*');
        $classpath     = "{$reportsPath};{$libPath};{$jdbcPath}";

        $reportTitle = 'LAPORAN DATA PELANGGAN';
        if ($statusFilter === 'aktif') {
            $reportTitle .= ' (STATUS AKTIF)';
        } elseif ($statusFilter === 'nonaktif') {
            $reportTitle .= ' (STATUS NONAKTIF)';
        }

        $cmd = sprintf(
            'java -cp %s reports.JasperRunner -i %s -o %s -f %s -d %s -P STATUS_FILTER="%s" -P REPORT_TITLE="%s" 2>&1',
            escapeshellarg($classpath),
            escapeshellarg($inputJrxml),
            escapeshellarg($outputBasePath),
            escapeshellarg($format),
            escapeshellarg($tempJsonFile),
            addslashes($statusFilter),
            addslashes($reportTitle)
        );

        $output = [];
        $returnVar = 0;
        exec($cmd, $output, $returnVar);

        // Hapus file JSON sementara
        if (File::exists($tempJsonFile)) {
            File::delete($tempJsonFile);
        }

        $generatedFile = $outputBasePath . '.' . ($format === 'xlsx' ? 'xlsx' : ($format === 'csv' ? 'csv' : 'pdf'));

        if ($returnVar !== 0 || !File::exists($generatedFile)) {
            $errorOutput = implode("\n", $output);
            Log::error("JasperRunner execution failed: {$errorOutput}");
            throw new \Exception("Gagal menghasilkan laporan JasperReport: " . ($errorOutput ?: 'File output tidak ditemukan.'));
        }

        return $generatedFile;
    }

    /**
     * Pratinjau (Preview) Laporan PDF JasperReport di Browser
     */
    public function preview(Request $request)
    {
        $statusFilter = $request->input('status', 'semua');

        try {
            $filePath = $this->executeJasperReport('pdf', $statusFilter);

            return response()->file($filePath, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="laporan_pelanggan.pdf"'
            ]);
        } catch (\Throwable $e) {
            Log::error('JasperReport PDF Preview Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return back()->with('error', 'Gagal memproses JasperReport: ' . $e->getMessage());
        }
    }

    /**
     * Download Laporan dalam format PDF, Excel (XLSX), atau CSV
     */
    public function download(Request $request)
    {
        $format = strtolower($request->input('format', 'pdf'));
        $statusFilter = $request->input('status', 'semua');

        if (!in_array($format, ['pdf', 'xlsx', 'csv'])) {
            $format = 'pdf';
        }

        try {
            $filePath = $this->executeJasperReport($format, $statusFilter);
            $fileName = 'Laporan_Pelanggan_' . date('Ymd_His') . '.' . $format;

            return response()->download($filePath, $fileName)->deleteFileAfterSend(true);
        } catch (\Throwable $e) {
            Log::error('JasperReport Download Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return back()->with('error', 'Gagal mendownload laporan: ' . $e->getMessage());
        }
    }
}

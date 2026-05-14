<?php
// app/Http/Controllers/Admin/PenilaianController.php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\Controller;
use App\Services\Admin\SmartService;


class PenilaianController extends Controller
{
    protected SmartService $smartService;
    
    public function __construct(SmartService $smartService)
    {
        $this->smartService = $smartService;
    }
    
    /**
     * Display a listing of assessments.
     */
    public function index()
    {
        $penilaian = $this->smartService->getAllAssessments(10);
        
        return view('admin.pages.penilaian.index', [
            'title' => 'Data Penilaian SMART',
            'penilaian' => $penilaian,
        ]);
    }
    
    /**
     * Show form for creating new assessment.
     */
    public function create(Request $request)
    {
        $pengajuanId = $request->input('pengajuan_id');
        
        if (!$pengajuanId) {
            return redirect()->route('admin.pengajuan.index')
                ->with('error', 'Parameter pengajuan_id tidak ditemukan');
        }
        
        try {
            // Get form data from service
            $formData = $this->smartService->getFormData($pengajuanId);
            
            // Check existing assessment
            $penilaianExists = DB::table('penilaian')
                ->where('pengajuan_id', $pengajuanId)
                ->exists();
            
            // Handle auto-save or existing data
            $nilaiTersimpan = [];
            $threshold = 65;
            
            if ($request->query('restore_auto_save')) {
                $autoSaveData = $this->smartService->getAutoSave($pengajuanId);
                if ($autoSaveData) {
                    $nilaiTersimpan = $autoSaveData['nilai'] ?? [];
                    $threshold = $autoSaveData['threshold'] ?? 65;
                    session()->flash('info', 'Data auto-save telah dipulihkan');
                }
            } elseif ($penilaianExists && $request->input('revisi')) {
                $penilaian = DB::table('penilaian')
                    ->where('pengajuan_id', $pengajuanId)
                    ->first();
                
                if ($penilaian) {
                    $nilaiTersimpan = DB::table('penilaian_detail')
                        ->where('penilaian_id', $penilaian->id)
                        ->pluck('nilai_input', 'kriteria_id')
                        ->toArray();
                    $threshold = $penilaian->threshold ?? 65;
                }
            }
            
            // Set default scores
            $defaultSkor = [];
            foreach ($formData['kriteria'] as $k) {
                if ($k->skala->isNotEmpty()) {
                    $defaultSkor[$k->id] = $nilaiTersimpan[$k->id] ?? $k->skala->first()->skor;
                }
            }
            
            return view('admin.pages.penilaian.create', array_merge($formData, [
                'nilaiTersimpan' => $nilaiTersimpan,
                'threshold' => $threshold,
                'defaultSkor' => $defaultSkor
            ]));
            
        } catch (\Exception $e) {
            return redirect()->route('admin.pengajuan.index')
                ->with('error', 'Gagal memuat data: ' . $e->getMessage());
        }
    }
    
    /**
     * Store a newly created assessment.
     */
    public function store(Request $request)
    {
        $request->validate([
            'pengajuan_id' => 'required|exists:pengajuan,id',
            'threshold' => 'required|numeric|min:0|max:100',
            'nilai' => 'required|array|min:1',
            'nilai.*' => 'required|numeric|min:0|max:100',
            'catatan' => 'nullable|string|max:1000'
        ]);
        
        $result = $this->smartService->storeAssessment([
            'pengajuan_id' => $request->pengajuan_id,
            'threshold' => $request->threshold,
            'nilai' => $request->nilai,
            'catatan' => $request->catatan
        ]);
        
        if ($result['success']) {
            return redirect()
                ->route('admin.penilaian.show', $result['penilaian_id'])
                ->with('success', "Penilaian SMART berhasil. Skor: {$result['skor_akhir']} - {$result['message']}");
        }
        
        return redirect()
            ->back()
            ->with('error', 'Terjadi kesalahan: ' . $result['message'])
            ->withInput();
    }
    
    /**
     * Display the specified assessment.
     */
    public function show(int $id)
{
    $penilaian = $this->smartService->getAssessmentData($id);
    
    if (!$penilaian) {
        return redirect()->route('admin.penilaian.index')
            ->with('error', 'Data penilaian tidak ditemukan');
    }
    
    // Get complete calculation details
    $calculationDetails = $this->smartService->getCalculationDetails($id);
    
    return view('admin.pages.penilaian.show', [
        'penilaian' => $penilaian,
        'detail' => $penilaian->details ?? collect(),
        'calculationDetails' => $calculationDetails
    ]);
}
    
    /**
     * Recalculate assessment score.
     */
    public function recalculate(int $id)
    {
        $result = $this->smartService->recalculate($id);
        
        if ($result['success']) {
            return redirect()
                ->route('admin.penilaian.show', $id)
                ->with('success', $result['message']);
        }
        
        return back()
            ->with('error', $result['message']);
    }
    
    /**
     * Auto save assessment data.
     */
    public function autoSave(Request $request)
    {
        $pengajuanId = $request->input('pengajuan_id');
        
        if (!$pengajuanId) {
            return response()->json([
                'success' => false,
                'message' => 'Pengajuan ID tidak ditemukan'
            ], 400);
        }
        
        $success = $this->smartService->saveAutoSave($pengajuanId, [
            'nilai' => $request->nilai ?? [],
            'catatan' => $request->catatan,
            'threshold' => $request->threshold ?? 65
        ]);
        
        if ($success) {
            return response()->json([
                'success' => true,
                'message' => 'Data auto-save tersimpan',
                'timestamp' => now()->toDateTimeString()
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Gagal menyimpan auto-save'
        ], 500);
    }
    
    /**
     * Clear auto save data.
     */
    public function clearAutoSave(Request $request)
    {
        $pengajuanId = $request->input('pengajuan_id');
        
        if ($pengajuanId) {
            $this->smartService->clearAutoSave($pengajuanId);
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Data auto-save berhasil dihapus'
        ]);
    }
}
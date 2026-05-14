<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DebiturPengajuanRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Allow only authenticated users
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            // Unit/Properti
            'id_properti' => ['required', 'exists:unit,id'],
            
            // Data Pribadi
            'nik' => ['required', 'string', 'size:16'],
            'tempat_lahir' => ['required', 'string', 'max:100'],
            'tanggal_lahir' => ['required', 'date', 'before:today'],
            'jenis_kelamin' => ['required', 'in:Laki-laki,Perempuan'],
            'agama' => ['nullable', 'string', 'max:50'],
            'status_pernikahan' => ['nullable', 'string', 'in:belum_menikah,sudah_menikah,cerai'],
            'jumlah_tanggungan' => ['nullable', 'integer', 'min:0', 'max:10'],
            'pendidikan' => ['nullable', 'string', 'max:100'],
            'kewarganegaraan' => ['nullable', 'string', 'in:WNI,WNA'],
            'nama_ibu' => ['required', 'string', 'max:255'],
            'no_kk' => ['nullable', 'string', 'size:16'],
            'nama_pasangan' => ['nullable', 'string', 'max:255'],
            'nik_pasangan' => ['nullable', 'string', 'size:16'],
            'alamat_ktp' => ['required', 'string'],
            'kota' => ['required', 'string', 'max:100'],
            'provinsi' => ['required', 'string', 'max:100'],
            'kode_pos' => ['nullable', 'string', 'max:10'],
            'status_tempat_tinggal' => ['nullable', 'string', 'in:Milik sendiri,Sewa,Keluarga'],
            'no_hp' => ['required', 'string', 'max:15'],
            'email' => ['required', 'email', 'max:255'],
            
            // Data Pekerjaan
            'status_pekerjaan' => ['nullable', 'string', 'in:Karyawan,PNS,TNI-Polri,Wiraswasta,Profesional'],
            'nama_perusahaan' => ['required_if:status_pekerjaan,Karyawan,PNS,TNI-Polri,Wiraswasta', 'nullable', 'string', 'max:255'],
            'bidang_usaha' => ['nullable', 'string', 'max:255'],
            'jabatan' => ['nullable', 'string', 'max:255'],
            'status_kepegawaian' => ['nullable', 'string', 'in:Tetap,Kontrak,Percobaan'],
            'lama_bekerja' => ['nullable', 'string', 'in:<1 th,1-2 th,2-5 th,>5 th'],
            'alamat_perusahaan' => ['nullable', 'string'],
            'telp_perusahaan' => ['nullable', 'string', 'max:15'],
            'npwp' => ['nullable', 'string', 'max:20'],
            'penghasilan_pokok' => ['required', 'numeric', 'min:0'],
            'tunjangan' => ['nullable', 'numeric', 'min:0'],
            'penghasilan_lain' => ['nullable', 'numeric', 'min:0'],
            
            // Data Keuangan
            'nama_bank' => ['required', 'string', 'max:100'],
            'nomor_rekening' => ['required', 'string', 'max:30'],
            'pemilik_rekening' => ['required', 'string', 'max:255'],
            'jenis_rekening' => ['nullable', 'string', 'in:tabungan,giro'],
            'rata_saldo' => ['nullable', 'numeric', 'min:0'],
            'rata_mutasi' => ['nullable', 'numeric', 'min:0'],
            'total_cicilan' => ['nullable', 'numeric', 'min:0'],
            'jumlah_kredit_aktif' => ['nullable', 'integer', 'min:0'],
            'limit_kartu_kredit' => ['nullable', 'numeric', 'min:0'],
            'tagihan_kartu_kredit' => ['nullable', 'numeric', 'min:0'],
            'memiliki_kpr_aktif' => ['nullable', 'string', 'in:Ya,Tidak'],
            'sisa_pokok_kpr' => ['nullable', 'numeric', 'min:0'],
            'status_kredit' => ['nullable', 'string', 'in:Lancar,DPK,Kurang lancar,Diragukan,Macet'],
            'pernah_gagal_bayar' => ['nullable', 'string', 'in:Tidak pernah,Pernah'],
            'aset_properti' => ['nullable', 'numeric', 'min:0'],
            'aset_kendaraan' => ['nullable', 'numeric', 'min:0'],
            'aset_tabungan' => ['nullable', 'numeric', 'min:0'],
            'aset_lainnya' => ['nullable', 'numeric', 'min:0'],
            
            // Data Pengajuan
            'dp' => ['nullable', 'numeric', 'min:0'],
            'tenor' => ['nullable', 'integer', 'min:1', 'max:30'],
            'tujuan_pembelian' => ['nullable', 'string', 'in:hunian_sendiri,investasi'],
            'sumber_dp' => ['nullable', 'string', 'in:Tabungan,Keluarga,Jual aset,Lainnya'],
            'catatan_debitur' => ['nullable', 'string', 'max:1000'],
            
            // Action untuk draft/submit
            'action' => ['nullable', 'string', 'in:draft,submit'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'nik.required' => 'NIK wajib diisi.',
            'nik.size' => 'NIK harus 16 digit.',
            'id_properti.required' => 'Silakan pilih unit properti.',
            'id_properti.exists' => 'Unit properti yang dipilih tidak valid.',
            'penghasilan_pokok.required' => 'Penghasilan pokok wajib diisi.',
            'penghasilan_pokok.numeric' => 'Penghasilan pokok harus berupa angka.',
            'no_hp.required' => 'Nomor HP wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'tanggal_lahir.before' => 'Tanggal lahir harus sebelum hari ini.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Clean up NIK and KK from non-numeric characters
        if ($this->has('nik')) {
            $this->merge([
                'nik' => preg_replace('/[^0-9]/', '', $this->nik),
            ]);
        }
        
        if ($this->has('no_kk')) {
            $this->merge([
                'no_kk' => preg_replace('/[^0-9]/', '', $this->no_kk),
            ]);
        }
        
        if ($this->has('no_hp')) {
            $this->merge([
                'no_hp' => preg_replace('/[^0-9]/', '', $this->no_hp),
            ]);
        }
    }
}
<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Repositories\Interfaces\SchoolIdentityRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Enums\JenjangPendidikan;
use App\Enums\StatusKepemilikan;
use App\Enums\Akreditasi;
use App\Enums\StatusSekolah;

class SchoolIdentityController extends Controller
{
    public function __construct(
        protected SchoolIdentityRepositoryInterface $repository
    ) {}

    /**
     * Menampilkan halaman konfigurasi lembaga.
     */
    public function index()
    {
        $school = $this->repository->first();
        $enums = [
            'jenjang' => JenjangPendidikan::cases(),
            'kepemilikan' => StatusKepemilikan::cases(),
            'akreditasi' => Akreditasi::cases(),
            'status' => StatusSekolah::cases(),
        ];
        return view('admin.settings.school.index', compact('school', 'enums'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'school_name' => 'required|string|max:255',
            'npsn' => 'nullable|string|max:20',
            'education_level' => 'nullable|string|max:50',
            'school_status' => 'nullable|string|max:20',
            'ownership_status' => 'nullable|string|max:255',
            'establishment_sk' => 'nullable|string|max:255',
            'establishment_date' => 'nullable|date',
            'operational_sk' => 'nullable|string|max:255',
            'tax_id' => 'nullable|string|max:50',
            'accreditation' => 'nullable|string|max:10',
            'accreditation_expiry_date' => 'nullable|date',
            'address' => 'nullable|string',
            'latitude' => 'nullable|string',
            'longitude' => 'nullable|string',
            'whatsapp' => 'nullable|string|max:20',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'headmaster_name' => 'nullable|string|max:255',
            'headmaster_nip' => 'nullable|string|max:50',
            'treasurer_name' => 'nullable|string|max:255',
            'treasurer_nip' => 'nullable|string|max:50',
            'operator_name' => 'nullable|string|max:255',
            'operator_nip' => 'nullable|string|max:50',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'stamp' => 'nullable|image|mimes:png|max:2048',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        try {
            $school = $this->repository->first();
            $data = $validated;

            // Handle File Uploads
            $fileFields = ['logo', 'stamp', 'profile_image'];
            foreach ($fileFields as $field) {
                if ($request->hasFile($field)) {
                    if ($school && $school->$field) {
                        Storage::delete('public/' . $school->$field);
                    }
                    $data[$field] = $request->file($field)->store('school', 'public');
                }
            }

            $this->repository->updateOrCreate($data);

            return response()->json([
                'message' => 'Konfigurasi lembaga berhasil diperbarui',
                'data' => $this->repository->first()
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal memperbarui data: ' . $e->getMessage()], 500);
        }
    }
}

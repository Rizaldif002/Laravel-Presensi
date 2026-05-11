<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePresensiRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'kelas_id'         => ['required', 'integer', 'exists:kelas_perkuliahans,id'],
            'latitude'         => ['required', 'numeric', 'between:-90,90'],
            'longitude'        => ['required', 'numeric', 'between:-180,180'],
            'is_dev_mode'      => ['required', 'boolean'],
            'is_mock_location' => ['required', 'boolean'],
            'face_match'       => ['required', 'boolean'],
            'face_confidence'  => ['required', 'numeric', 'between:0,1'],
            'foto_selfie'      => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'kelas_id.exists'        => 'Kelas tidak ditemukan.',
            'latitude.between'       => 'Koordinat latitude tidak valid.',
            'longitude.between'      => 'Koordinat longitude tidak valid.',
            'face_confidence.between' => 'Nilai face confidence harus antara 0 dan 1.',
        ];
    }
}

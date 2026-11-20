<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

abstract class LocationRequest extends FormRequest
{
    // Batas radius absen (meter). Sesuaikan bila perlu.
    private const MIN_RADIUS_M = 10;
    private const MAX_RADIUS_M = 1000;

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required', 'string', 'max:100',
                Rule::unique('locations', 'name')
                    ->where('company_id', $this->user()->company_id)
                    ->ignore($this->route('location')),
            ],
            'address' => ['nullable', 'string', 'max:500'],
            'latitude' => ['required', 'numeric', 'between:-90,90', 'decimal:0,6'],
            'longitude' => ['required', 'numeric', 'between:-180,180', 'decimal:0,6'],
            'radius_m' => ['required', 'integer', 'between:'.self::MIN_RADIUS_M.','.self::MAX_RADIUS_M],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => 'nama kantor',
            'address' => 'alamat',
            'latitude' => 'lintang',
            'longitude' => 'bujur',
            'radius_m' => 'radius absen',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'required' => ':attribute wajib diisi.',
            'max' => ':attribute maksimal :max karakter.',
            'unique' => ':attribute sudah dipakai di perusahaan Anda.',
            'numeric' => ':attribute harus berupa angka.',
            'integer' => ':attribute harus berupa bilangan bulat.',
            'between' => ':attribute harus di antara :min dan :max.',
            'decimal' => ':attribute maksimal :decimal angka di belakang koma.',
        ];
    }
}

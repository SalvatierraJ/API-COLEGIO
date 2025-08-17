<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInstitucionRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'cliente.nombre'       => ['sometimes','required','string','max:255'],
            'cliente.rut'          => ['sometimes','required','string','max:20'],
            'cliente.comunaId'     => ['sometimes','required','integer','exists:comunas,id'],
            'cliente.telefono'     => ['sometimes','nullable','string','max:50'],
            'cliente.direccion'    => ['sometimes','nullable','string','max:255'],
            'cliente.fecha_inicio' => ['sometimes','nullable','date'],

            'colegios'                 => ['sometimes','array'],
            'colegios.*.id'            => ['nullable'],
            'colegios.*.nombre'        => ['required','string','max:255'],
            'colegios.*.rut'           => ['required','string','max:20'],
            'colegios.*.comunaId'      => ['required','integer','exists:comunas,id'],
            'colegios.*.telefono'      => ['nullable','string','max:50'],
            'colegios.*.direccion'     => ['nullable','string','max:255'],

            'usuarios'                 => ['sometimes','array'],
            'usuarios.*.nombres'       => ['required','string','max:255'],
            'usuarios.*.apellidos'     => ['required','string','max:255'],
            'usuarios.*.rut'           => ['required','string','max:20'],
            'usuarios.*.telefono'      => ['nullable','string','max:50'],
            'usuarios.*.correo'        => ['nullable','email'],
            'usuarios.*.colegioId'     => ['required'],
        ];
    }
}

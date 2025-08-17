<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInstitucionRequest extends FormRequest
{
     public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'cliente.nombre'       => ['required','string','max:255'],
            'cliente.rut'          => ['required','string','max:20'],
            'cliente.comunaId'     => ['required','integer','exists:comunas,id'],
            'cliente.telefono'     => ['nullable','string','max:50'],
            'cliente.direccion'    => ['nullable','string','max:255'],
            'cliente.fecha_inicio' => ['nullable','date'],

            'colegios'                 => ['array'],
            'colegios.*.id'            => ['nullable'],
            'colegios.*.nombre'        => ['required','string','max:255'],
            'colegios.*.rut'           => ['required','string','max:20'],
            'colegios.*.comunaId'      => ['required','integer','exists:comunas,id'],
            'colegios.*.telefono'      => ['nullable','string','max:50'],
            'colegios.*.direccion'     => ['nullable','string','max:255'],

            'usuarios'                 => ['array'],
            'usuarios.*.nombres'       => ['required','string','max:255'],
            'usuarios.*.apellidos'     => ['required','string','max:255'],
            'usuarios.*.rut'           => ['required','string','max:20'],
            'usuarios.*.telefono'      => ['nullable','string','max:50'],
            'usuarios.*.correo'        => ['nullable','email'],
            'usuarios.*.colegioId'     => ['required'],
        ];
    }

    public function attributes(): array
    {
        return [
            'cliente.nombre' => 'nombre del cliente',
            'cliente.rut' => 'RUT del cliente',
            'cliente.comunaId' => 'comuna del cliente',
        ];
    }
}

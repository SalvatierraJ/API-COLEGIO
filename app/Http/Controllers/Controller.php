<?php

namespace App\Http\Controllers;

abstract class Controller
{
    /**
     * @OA\Info(
     *   title="API Colegios",
     *   version="1.0.0",
     *   description="Documentación de la API"
     * )
     * @OA\Server(
     *   url="/api",
     *   description="Servidor principal"
     * )
     * @OA\SecurityScheme(
     *   securityScheme="bearerAuth",
     *   type="http",
     *   scheme="bearer",
     *   bearerFormat="JWT"
     * )
     */
}

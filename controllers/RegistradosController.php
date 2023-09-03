<?php

namespace Controllers;

use Model\Registro;
use MVC\Router;
use Classes\Paginacion;
use Model\Paquete;
use Model\Usuario;

class RegistradosController
{
    public static function index(Router $router)
    {
        if (!is_admin()) {
            header('location: /login');
        }

        $pagina_actual = $_GET['page'];
        $pagina_actual = filter_var($pagina_actual, FILTER_VALIDATE_INT);
        /* validamos que si sea un numero entero y que no se negativo */
        if (!$pagina_actual || $pagina_actual < 1) {
            header('location: /admin/registrados?page=1');
        }
        $registros_por_pagina = 2; /* yo lo defino */
        $total = Registro::total();

        $paginacion = new Paginacion($pagina_actual, $registros_por_pagina, $total);
        if ($paginacion->total_paginas() < $pagina_actual) {
            header('location: /admin/registrados?page=1');
        }
        $registros = Registro::paginar($registros_por_pagina, $paginacion->offset());
        foreach($registros as $registro) {
            $registro->usuario = Usuario::find($registro->usuario_id);
            $registro->paquete = Paquete::find($registro->paquete_id);
        }

        // Render a la vista 
        $router->render('admin/registrados/index', [
            'titulo' => 'Usuarios Registrados',
            'registros' => $registros,
            'paginacion' => $paginacion->paginacion(),
        ]);
    }
}

<?php

namespace Controllers;

use Model\Evento;
use Model\Registro;
use Model\Usuario;
use MVC\Router;

class DashboardController
{
    public static function index(Router $router)
    {
        if(!is_admin()) {
            header('location: /login');
        }

        /* Obtener ultimos registrados */
        $registros = Registro::get(5);
        foreach($registros as $registro){
            $registro->usuario = Usuario::find($registro->usuario_id);
        }

        /* calcular los ingresos */
        $virtuales = Registro::total('paquete_id', 2);
        $presenciales = Registro::total('paquete_id', 1);

        /* en lugar de 449 y 199 son el dinero total ya contando el impuesto de paypay y el iva (en este caso el precio ya incluye el iva) */
        $ingresos = ($virtuales * 46.41) + ($presenciales * 189.54);

        /* obtener eventos con más y menos lugares disponibles */
        $menos_disponibles = Evento::ordenarLimite('disponibles', 'ASC', 5);
        $mas_disponibles = Evento::ordenarLimite('disponibles', 'DESC', 5);

        // Render a la vista 
        $router->render('admin/dashboard/index', [
            'titulo' => 'Panel de Administración',
            'registros' => $registros,
            'ingresos' => $ingresos,
            'menos_disponibles' => $menos_disponibles,
            'mas_disponibles' => $mas_disponibles,
        ]);
    }
}

<?php

namespace Controllers;

use MVC\Router;
use Model\Categoria;
use Model\Dia;
use Model\Evento;
use Model\Ponente;
use Classes\Paginacion;
use Model\Hora;

class EventosController
{
    public static function index(Router $router)
    {
        if(!is_admin()) {
            header('location: /login');
        }

        $pagina_actual = $_GET['page'];
        $pagina_actual = filter_var($pagina_actual, FILTER_VALIDATE_INT);
        /* validamos que si sea un numero entero y que no se negativo */
        if(!$pagina_actual || $pagina_actual < 1) {
            header('location: /admin/eventos?page=1');
        }

        $registros_por_pagina = 10; /* yo lo defino */
        $total = Evento::total();

        $paginacion = new Paginacion($pagina_actual, $registros_por_pagina, $total);
        // if($paginacion->total_paginas() < $pagina_actual) {
        //     header('location: /admin/eventos?page=1');
        // }
        $eventos = Evento::paginar($registros_por_pagina, $paginacion->offset());
        
        foreach($eventos as $evento) {
            $evento->categoria = Categoria::find($evento->categoria_id);
            $evento->dia = Dia::find($evento->dia_id);
            $evento->hora = Hora::find($evento->hora_id);
            $evento->ponente = Ponente::find($evento->ponente_id);
        }
        
        // Render a la vista 
        $router->render('admin/eventos/index', [
            'titulo' => 'Conferencias y Workshops',
            'eventos' => $eventos,
            'paginacion' => $paginacion->paginacion(),
        ]);
    }

    public static function crear(Router $router)
    {
        if(!is_admin()) {
            header('location: /login');
        }

        $alertas = [];

        $categorias = Categoria::all();
        $dias = Dia::all('ASC');
        $horas = Hora::all('ASC');

        $evento = new Evento();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $evento->sincronizar($_POST);

            $alertas = $evento->validar();

            if(empty($alertas)) {
                $resultado = $evento->guardar();

                if($resultado) {
                    header('location: /admin/eventos');
                }
            }
        }
        
        // Render a la vista 
        $router->render('admin/eventos/crear', [
            'titulo' => 'Registrar Eventos',
            'alertas' => $alertas,
            'categorias' => $categorias,
            'dias' => $dias,
            'horas' => $horas,
            'evento' => $evento,
        ]);
    }

    public static function editar(Router $router)
    {
        if (!is_admin()) {
            header('location: /login');
        }

        $alertas = [];
        $id = $_GET['id'];
        $id = filter_var($id, FILTER_VALIDATE_INT);

        if (!$id) {
            header('location: /admin/eventos');
        }

        $categorias = Categoria::all('ASC');
        $dias = Dia::all('ASC');
        $horas = Hora::all('ASC');

        $evento = Evento::find($id);
        if (!$evento) {
            header('location: /admin/eventos');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!is_admin()) {
                header('location: /login');
            }

            $evento->sincronizar($_POST);

            /* Validar */
            $alertas = $evento->validar();

            /* Guardar el registro */
            if (empty($alertas)) {

                /* Guardar en la bd */
                $resultado = $evento->guardar();

                if ($resultado) {
                    header('location: /admin/eventos');
                }
            }
        }

        // Render a la vista 
        $router->render('admin/eventos/actualizar', [
            'titulo' => 'Editar Eventos',
            'alertas' => $alertas,
            'evento' => $evento,
            'categorias' => $categorias,
            'dias' => $dias,
            'horas' => $horas,
        ]);
    }

    public static function eliminar()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!is_admin()) {
                header('location: /login');
            }
            $id = $_POST['id'];

            $evento = Evento::find($id);

            if (!isset($ponente)) {
                header('location: /admin/eventos');
            }

            $resultado = $evento->eliminar();

            if ($resultado) {
                header('location: /admin/eventos');
            }
        }
    }
}

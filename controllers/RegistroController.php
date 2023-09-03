<?php

namespace Controllers;

use Model\Categoria;
use Model\Dia;
use Model\Evento;
use Model\EventosRegistros;
use Model\Gift;
use Model\Hora;
use Model\Paquete;
use Model\Ponente;
use Model\Registro;
use Model\Usuario;
use MVC\Router;

class RegistroController
{
    public static function crear(Router $router)
    {
        if (!is_auth()) {
            header('location: /');
            return;
        }

        /* verificar si el usuario ya esta registrado */
        $registro = Registro::where('usuario_id', $_SESSION['id']);
        if (isset($registro) && ($registro->paquete_id === "3" || $registro->paquete_id === "2")) {
            header('location: /boleto?id=' . urlencode($registro->token));
            return;
        }
        if(isset($registro) && $registro->paquete_id === "1"){
            header('Location: /finalizar-registro/conferencias');
            return;
        }
        /* Redireccionar a boleto virtual en caso de haber finalizado su registro 
        o elegir los eventos para finalizar el registro presencial */
        // if ($registro->regalo_id === 1 && $registro->paquete_id === "2") {
        //     header('Location: /finalizar-registro/conferencias');
        // } else if ($registro->regalo_id != 1) {
        //     header('Location: /boleto?id=' . urlencode($registro->token));
        // }

        // Render a la vista 
        $router->render('registro/crear', [
            'titulo' => 'Finalizar el Registro',
        ]);
    }

    public static function gratis()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!is_auth()) {
                header('location: /');
                return;
            }

            /* verificar si el usuario ya esta registrado */
            $registro = Registro::where('usuario_id', $_SESSION['id']);
            if (isset($registro) && $registro->paquete_id === "3") {
                header('location: /boleto?id=' . urlencode($registro->token));
                return;
            }

            $token = substr(md5(uniqid(rand(), true)), 0, 8); /* cortamos la cadena a solo 8 caracteres */

            /* crear registro */

            $datos = array(
                'paquete_id' => 3,
                'pago_id' => '',
                'token' => $token,
                'usuario_id' => $_SESSION['id']
            );

            $registro = new Registro($datos);
            $resultado = $registro->guardar();

            if ($resultado) {
                header('location: /boleto?id=' . urlencode($registro->token));
                return;
            }
        }
    }

    public static function pagar()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!is_auth()) {
                header('location: /login');
                return;
            }

            /* borro la validacion por si el usuario se arrepiente del paquete gratis y deasea comprar uno */

            /* validar que el post no venga vacio */
            if (empty($_POST)) {
                echo json_encode([]);
                return;
            }

            /* crear registro */
            $datos = $_POST;
            $datos['token'] = substr(md5(uniqid(rand(), true)), 0, 8);
            $datos['usuario_id'] = $_SESSION['id'];

            try {
                $registro = new Registro($datos);
                $resultado = $registro->guardar();
                echo json_encode($resultado);
            } catch (\Throwable $th) {
                echo json_encode([
                    'resultado' => 'error'
                ]);
            }
        }
    }

    public static function conferencias(Router $router)
    {
        if (!is_auth()) {
            header('location: /login');
            return;
        }
        $usuario_id = $_SESSION['id'];
        $registro = Registro::where('usuario_id', $usuario_id);
        echo "<pre>";
        var_dump($registro);
        echo "</pre>";
        die();

        if (isset($registro) && $registro->paquete_id === "2") {
            header('location: /boleto?id=' . urlencode($registro->token));
            return;
        }

        /* solo los usuarios con el plan presencial pueden ver esta seccion */
        // if ($registro->paquete_id !== "1") {
        //     header('location: /');
        //     return;
        // }

        if (isset($registro->regalo_id) && $registro->paquete_id === "1") {
            header('location: /boleto?id=' . urlencode($registro->token));
            return;
        }

        $eventos = Evento::ordenar('hora_id', 'ASC');

        $eventos_formateados = [];
        foreach ($eventos as $evento) {
            $evento->categoria = Categoria::find($evento->categoria_id);
            $evento->dia = Dia::find($evento->dia_id);
            $evento->hora = Hora::find($evento->hora_id);
            $evento->ponente = Ponente::find($evento->ponente_id);

            if ($evento->dia_id === "1" && $evento->categoria_id === "1") {
                $eventos_formateados['conferencias_v'][] = $evento;
            }
            if ($evento->dia_id === "2" && $evento->categoria_id === "1") {
                $eventos_formateados['conferencias_s'][] = $evento;
            }
            if ($evento->dia_id === "1" && $evento->categoria_id === "2") {
                $eventos_formateados['workshops_v'][] = $evento;
            }
            if ($evento->dia_id === "2" && $evento->categoria_id === "2") {
                $eventos_formateados['workshops_s'][] = $evento;
            }
        }

        $regalos = Gift::all('ASC');

        /* manejando el registro mediante POST */
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            /* revisar que el usuario este autenticado */
            if (!is_auth()) {
                header('location: /login');
                return;
            }

            $eventos = explode(',', $_POST['eventos']);
            if (empty($evento)) {
                echo json_encode(['resultado' => false]);
                return;
            }

            /* obtener el registro de usuario */
            $registro = Registro::where('usuario_id', $_SESSION['id']);
            if (!isset($registro) || $registro->paquete_id !== "1") {
                echo json_encode(['resultado' => false]);
                return;
            }

            $eventos_array = [];

            /* validar la disponibilidad de los eventos seleccionados */
            foreach ($eventos as $evento_id) {
                $evento = Evento::find($evento_id);
                /* como te comerias una ballena, una mordida a la vez */

                /* comprobar que el evento exista */
                if (!isset($evento) || $evento->disponibles === '0') {
                    echo json_encode(['resultado' => false]);
                    return;
                }
                $eventos_array[] = $evento;

                /* si hacemos la operacion de restar un lugar a disponibles y si de los 5 eventos solo 4 se pudieron marcaria un error */
            }

            /* es mejor mantener un momento la informacion en memoria que hacer una consulta a la bd */
            foreach ($eventos_array as $evento) {
                $evento->disponibles -= 1;
                $evento->guardar();

                /* almacenar el registro */
                $datos = [
                    'evento_id' => (int) $evento->id,
                    'registro_id' => (int) $registro->id,
                ];

                $registro_usuario = new EventosRegistros($datos);
                $registro_usuario->guardar();

                /* Almacenar el regalo */
                $registro->sincronizar(['regalo_id' => $_POST['regalo_id']]);
                $resultado = $registro->guardar();

                if ($resultado) {
                    echo json_encode([
                        'resultado' => $resultado,
                        'token' => $registro->token
                    ]);
                } else {
                    echo json_encode(['resultado' => false]);
                }
                return;
            }
        }

        // Render a la vista 
        $router->render('registro/conferencias', [
            'titulo' => 'Elige Workshops y Conferencias',
            'eventos' => $eventos_formateados,
            'regalos' => $regalos,
        ]);
    }

    public static function boleto(Router $router)
    {
        /* validar url */
        $id = $_GET['id'];

        if (!$id || !strlen($id) === 8) {
            header('location: /');
            return;
        }

        /* buscar id en la bd */
        $registro = Registro::where('token', $id);
        if (empty($registro)) {
            header('location: /');
            return;
        }

        /* llenar las tablas de referencias */
        $registro->usuario = Usuario::find($registro->usuario_id);
        $registro->paquete = Paquete::find($registro->paquete_id);

        // Render a la vista 
        $router->render('registro/boleto', [
            'titulo' => 'Asistencia de DevWebCamp',
            'registro' => $registro
        ]);
    }
}

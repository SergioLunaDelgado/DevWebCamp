<?php

namespace Controllers;

use Classes\Paginacion;
use Model\Ponente;
use MVC\Router;

use Intervention\Image\ImageManagerStatic as Image;

class PonentesController
{
    public static function index(Router $router)
    {
        if (!is_admin()) {
            header('location: /login');
        }

        $pagina_actual = $_GET['page'];
        $pagina_actual = filter_var($pagina_actual, FILTER_VALIDATE_INT);
        /* validamos que si sea un numero entero y que no se negativo */
        if(!$pagina_actual || $pagina_actual < 1) {
            header('location: /admin/ponentes?page=1');
        }
        $registros_por_pagina = 6; /* yo lo defino */
        $total = Ponente::total();

        $paginacion = new Paginacion($pagina_actual, $registros_por_pagina, $total);
        // if($paginacion->total_paginas() < $pagina_actual) {
        //     header('location: /admin/ponentes?page=1');
        // }
        $ponentes = Ponente::paginar($registros_por_pagina, $paginacion->offset());

        // Render a la vista 
        $router->render('admin/ponentes/index', [
            'titulo' => 'Ponentes / Conferencistas',
            'ponentes' => $ponentes,
            'paginacion' => $paginacion->paginacion(),
        ]);
    }

    public static function crear(Router $router)
    {
        if (!is_admin()) {
            header('location: /login');
        }

        $alertas = [];

        $ponente = new Ponente;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!is_admin()) {
                header('location: /login');
            }

            /* leer imagenes */
            if (!empty($_FILES['imagen']['tmp_name'])) {
                $carpeta_imagenes = '../public/img/speakers';

                /* crear la carpeta si no existe */
                if (!is_dir($carpeta_imagenes)) {
                    mkdir($carpeta_imagenes, 0777, true); /* 0755 nivel de permisos, creacion de subdominios */
                }

                $imagen_png = Image::make($_FILES['imagen']['tmp_name'])->fit(800, 800)->encode('png', 80);
                $imagen_webp = Image::make($_FILES['imagen']['tmp_name'])->fit(800, 800)->encode('webp', 80);
                /* no soporta avif */

                $nombre_imagen = md5(uniqid(rand(), true));

                $_POST['imagen'] = $nombre_imagen;
            }

            /* antes de sincronizar post tenemos que convertir el arreglo de redes a string */
            $_POST['redes'] = json_encode($_POST['redes'], JSON_UNESCAPED_SLASHES);

            $ponente->sincronizar($_POST);

            /* Validar */
            $alertas = $ponente->validar();

            /* Guardar el registro */
            if (empty($alertas)) {
                /* Guardar la imagen */
                $imagen_png->save($carpeta_imagenes . '/' . $nombre_imagen . ".png");
                $imagen_webp->save($carpeta_imagenes . '/' . $nombre_imagen . ".webp");

                /* Guardar en la bd */
                $resultado = $ponente->guardar();

                if ($resultado) {
                    header('location: /admin/ponentes');
                }
            }
        }

        // Render a la vista 
        $router->render('admin/ponentes/crear', [
            'titulo' => 'Registrar Ponentes',
            'alertas' => $alertas,
            'ponente' => $ponente,
            'redes' => json_decode($ponente->redes)
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
            header('location: /admin/ponentes');
        }

        $ponente = Ponente::find($id);
        if (!$ponente) {
            header('location: /admin/ponentes');
        }

        $ponente->imagen_actual = $ponente->imagen;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!is_admin()) {
                header('location: /login');
            }

            if (!empty($_FILES['imagen']['tmp_name'])) {
                $carpeta_imagenes = '../public/img/speakers';

                /* Eliminar la imagen previa */
                unlink($carpeta_imagenes . '/' . $ponente->imagen_actual . ".png");
                unlink($carpeta_imagenes . '/' . $ponente->imagen_actual . ".webp");

                /* crear la carpeta si no existe */
                if (!is_dir($carpeta_imagenes)) {
                    mkdir($carpeta_imagenes, 0777, true); /* 0755 nivel de permisos, creacion de subdominios */
                }

                $imagen_png = Image::make($_FILES['imagen']['tmp_name'])->fit(800, 800)->encode('png', 80);
                $imagen_webp = Image::make($_FILES['imagen']['tmp_name'])->fit(800, 800)->encode('webp', 80);
                /* no soporta avif */

                $nombre_imagen = md5(uniqid(rand(), true));

                $_POST['imagen'] = $nombre_imagen;
            } else {
                $_POST['imagen'] = $ponente->imagen_actual;
            }

            /* antes de sincronizar post tenemos que convertir el arreglo de redes a string */
            $_POST['redes'] = json_encode($_POST['redes'], JSON_UNESCAPED_SLASHES);
            $ponente->sincronizar($_POST);

            /* Validar */
            $alertas = $ponente->validar();

            /* Guardar el registro */
            if (empty($alertas)) {
                if (isset($nombre_imagen)) {
                    /* Guardar la imagen */
                    $imagen_png->save($carpeta_imagenes . '/' . $nombre_imagen . ".png");
                    $imagen_webp->save($carpeta_imagenes . '/' . $nombre_imagen . ".webp");
                }

                /* Guardar en la bd */
                $resultado = $ponente->guardar();

                if ($resultado) {
                    header('location: /admin/ponentes');
                }
            }
        }

        // Render a la vista 
        $router->render('admin/ponentes/actualizar', [
            'titulo' => 'Actualizar Ponentes',
            'alertas' => $alertas,
            'ponente' => $ponente,
            'redes' => json_decode($ponente->redes)
        ]);
    }

    public static function eliminar()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!is_admin()) {
                header('location: /login');
            }
            $id = $_POST['id'];

            $ponente = Ponente::find($id);

            if (!isset($ponente)) {
                header('location: /admin/ponentes');
            }

            $resultado = $ponente->eliminar();

            if ($resultado) {
                header('location: /admin/ponentes');
            }
        }
    }
}

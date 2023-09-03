<?php 

namespace Controllers;

use Model\EventoHorario;
use Model\Ponente;

class APIPonentesController {
    public static function index() {
        if(!is_admin()) {
            echo json_encode([]);
            return;
        }
        
        $ponentes = Ponente::all();

        echo json_encode($ponentes);
    }

    public static function ponente() {
        if(!is_admin()) {
            echo json_encode([]);
            return;
        }
        
        $id = $_GET['id'];
        $id = filter_var($id, FILTER_VALIDATE_INT);

        if(!$id || $id < 1) {
            echo json_encode([]);
            return;
        }

        $ponente = Ponente::find($id);
        echo json_encode($ponente, JSON_UNESCAPED_SLASHES);
    }
}

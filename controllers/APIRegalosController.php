<?php
 
namespace Controllers;

use Model\Gift;
use Model\Registro;
 
class APIRegalosController {
 
    public static function index() {
 
        if(!is_admin()) {
            echo json_encode([]);
            return;
        }
 
        $regalos = Gift::all();
 
        foreach($regalos as $regalo) {
            $regalo->total = Registro::totalArray(['regalo_id' => $regalo->id, 'paquete_id' => "1"]);
        }
 
        echo json_encode($regalos);
        return;
        
    }
}
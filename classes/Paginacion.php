<?php 

namespace Classes;

class Paginacion {
    public $pagina_actual;
    public $registros_por_pagina;
    public $total_registros;

    public function __construct($pagina_actual = 1, $registros_por_pagina = 6, $total_registros = 0)
    {
        /* castear un valor */
        $this->pagina_actual = (int) $pagina_actual;
        $this->registros_por_pagina = (int) $registros_por_pagina;
        $this->total_registros = (int) $total_registros;
    }

    public function offset() {
        return $this->registros_por_pagina * ($this->pagina_actual - 1);
    }

    public function total_paginas() {
        /* ceil redondea hacia arriba */
        return ceil($this->total_registros / $this->registros_por_pagina);
    }

    /* evalua si se puede o no */
    public function pagina_anterior() {
        $anterior = $this->pagina_actual - 1;

        return ($anterior > 0) ? $anterior : false;
    }

    /* evalua si se puede o no */
    public function pagina_siguiente() {
        $siguiente = $this->pagina_actual + 1;

        return ($siguiente <= $this->total_paginas()) ? $siguiente : false;
    }

    /* imprime resultados en caso de poder */
    public function enlace_anterior() {
        $html = '';
        if($this->pagina_anterior()) {
            $html .= "<a href='?page={$this->pagina_anterior()}' class='paginacion__enlace paginacion__enlace--texto'>&laquo; Anterior</a>";
        }

        return $html;
    }

    /* imprime resultados en caso de poder */
    public function enlace_siguiente() {
        $html = '';
        if($this->pagina_siguiente()) {
            $html .= "<a href='?page={$this->pagina_siguiente()}' class='paginacion__enlace paginacion__enlace--texto'>Siguiente &raquo;</a>";
        }

        return $html;
    }

    public function numeros_paginas() {
        $html = '';
        for($i = 1; $i <= $this->total_paginas(); $i++) {
            if($i == $this->pagina_actual) {
                $html .= "<span class='paginacion__enlace paginacion__enlace--actual'>{$i}</span>";
            } else {
                $html .= "<a href='?page={$i}' class='paginacion__enlace paginacion__enlace--numero'>{$i}</a>";
            }
        }

        return $html;
    }

    /* contenedor */
    public function paginacion() {
        $html = '';
        if($this->total_registros > 1) {
            $html .= "<div class='paginacion'>";
            $html .= $this->enlace_anterior();
            $html .= $this->numeros_paginas();
            $html .= $this->enlace_siguiente();
            $html .= "</div>";
        }

        return $html;
    }
}
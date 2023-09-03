<?php
/* la key nos dice el estado, si es de error o exito */
foreach ($alertas as $key => $alerta) :
    foreach ($alerta as $mensaje) :
?>
        <div class="alerta alerta__<?php echo $key; ?>"><?php echo $mensaje; ?></div>
<?php
    endforeach;
endforeach;
?>
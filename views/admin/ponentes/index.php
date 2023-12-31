<h2 class="dashboard__heading"><?php echo $titulo ?></h2>

<div class="dashboard__contenedor-boton">
    <a href="/admin/ponentes/crear" class="dashboard__boton">
        <i class="fa-solid fa-circle-plus"></i>
        Añadir Ponente
    </a>
</div>

<div class="dashboard__contenedor">
    <?php if (!empty($ponentes)) : ?>
        <table class="table">
            <thead class="table__thead">
                <tr>
                    <th class="table__th" scope="col">Nombre</th>
                    <th class="table__th" scope="col">Ubicación</th>
                    <th class="table__th" scope="col"></th>
                </tr>
            </thead>
            <tbody class="table__tbody">
                <?php foreach ($ponentes as $ponente) : ?>
                    <tr class="table__tr">
                        <td class="table__td">
                            <?php echo $ponente->nombre . ' ' . $ponente->apellido; ?>
                        </td>
                        <td class="table__td">
                            <?php echo $ponente->ciudad . ', ' . $ponente->pais; ?>
                        </td>
                        <td class="table__td--acciones">
                            <a href="/admin/ponentes/editar?id=<?php echo $ponente->id ?>" class="table__acciones table__acciones--editar">
                                <i class="fa-solid fa-user-pen"></i>    
                                Editar
                            </a>
                            <form class="table__formulario" method="POST" action="/admin/ponentes/eliminar">
                                <input type="hidden" name="id" value="<?php echo $ponente->id; ?>">
                                <button type="submit" class="table__acciones table__acciones--eliminar">
                                    <i class="fa-solid fa-circle-xmark"></i>
                                    Eliminar
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else : ?>
        <p class="text-center">No Hay Ponentes Aún</p>
    <?php endif; ?>
</div>

<?php echo $paginacion; ?>

<h2 class="dashboard__heading"><?php echo $titulo ?></h2>

<div class="dashboard__contenedor-boton">
    <a href="/admin/eventos/crear" class="dashboard__boton">
        <i class="fa-solid fa-circle-plus"></i>
        Registrar Evento
    </a>
</div>

<div class="dashboard__contenedor">
    <?php if (!empty($eventos)) : ?>
        <table class="table">
            <thead class="table__thead">
                <tr>
                    <th class="table__th" scope="col">Nombre</th>
                    <th class="table__th" scope="col">Tipo</th>
                    <th class="table__th" scope="col">Dia y Hora</th>
                    <th class="table__th" scope="col">Ponente</th>
                    <th class="table__th" scope="col"></th>
                </tr>
            </thead>
            <tbody class="table__tbody">
                <?php foreach ($eventos as $evento) : ?>
                    <tr class="table__tr">
                        <td class="table__td">
                            <?php echo $evento->nombre ?>
                        </td>
                        <td class="table__td">
                            <?php echo $evento->categoria->nombre ?>
                        </td>
                        <td class="table__td">
                            <?php echo $evento->dia->nombre . ' ' . $evento->hora->hora; ?>
                        </td>
                        <td class="table__td">
                            <?php echo $evento->ponente->nombre . ' ' . $evento->ponente->apellido; ?>
                        </td>
                        <td class="table__td--acciones">
                            <a href="/admin/eventos/editar?id=<?php echo $evento->id ?>" class="table__acciones table__acciones--editar">
                                <i class="fa-solid fa-pencil"></i>    
                                Editar
                            </a>
                            <form class="table__formulario" method="POST" action="/admin/eventos/eliminar">
                                <input type="hidden" name="id" value="<?php echo $evento->id; ?>">
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
        <p class="text-center">No Hay Eventos Aún</p>
    <?php endif; ?>
</div>

<?php echo $paginacion; ?>
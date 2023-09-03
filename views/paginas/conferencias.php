<div class="agenda">
    <h2 class="agenda__heading"><?php echo $titulo ?></h2>
    <p class="agenda__descripcion">Talleres y Conferencias dictados por expertos en desarrollo web</p>

    <div class="eventos">
        <h3 class="eventos__heading">&lt;Conferencias/></h3>
        <p class="eventos__fecha">Lunes 18 de Diciembre</p>

        <div class="eventos__listados slider swiper">
            <!-- Aunque estemos usando metodologia BEM tenemos que llamar esta clase de la libreria swiper -->
            <div class="swiper-wrapper">
                <?php foreach ($eventos['conferencias_v'] as $evento) : ?>
                    <?php include __DIR__ . '/../../views/templates/evento.php' ?>
                <?php endforeach; ?>
            </div>
            <div class="swiper-button-next"></div>
            <div class="swiper-button-prev"></div>
        </div>

        <p class="eventos__fecha">Martes 19 de Diciembre</p>
        <div class="eventos__listados slider swiper">
            <!-- Aunque estemos usando metodologia BEM tenemos que llamar esta clase de la libreria swiper -->
            <div class="swiper-wrapper">
                <?php foreach ($eventos['conferencias_s'] as $evento) : ?>
                    <?php include __DIR__ . '/../../views/templates/evento.php' ?>
                <?php endforeach; ?>
            </div>
            <div class="swiper-button-next"></div>
            <div class="swiper-button-prev"></div>
        </div>

    </div>
    <div class="eventos eventos--workshops">
        <h3 class="eventos__heading">&lt;Workshops/></h3>
        <p class="eventos__fecha">Lunes 18 de Diciembre</p>

        <div class="eventos__listados slider swiper">
            <!-- Aunque estemos usando metodologia BEM tenemos que llamar esta clase de la libreria swiper -->
            <div class="swiper-wrapper">
                <?php foreach ($eventos['workshops_v'] as $evento) : ?>
                    <?php include __DIR__ . '/../../views/templates/evento.php' ?>
                <?php endforeach; ?>
            </div>
            <div class="swiper-button-next"></div>
            <div class="swiper-button-prev"></div>
        </div>

        <p class="eventos__fecha">Martes 19 de Diciembre</p>
        <div class="eventos__listados slider swiper">
            <!-- Aunque estemos usando metodologia BEM tenemos que llamar esta clase de la libreria swiper -->
            <div class="swiper-wrapper">
                <?php foreach ($eventos['workshops_s'] as $evento) : ?>
                    <?php include __DIR__ . '/../../views/templates/evento.php' ?>
                <?php endforeach; ?>
            </div>
            <div class="swiper-button-next"></div>
            <div class="swiper-button-prev"></div>
        </div>

    </div>

</div>
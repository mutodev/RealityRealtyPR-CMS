<p style="font-weight: bold;">

    <?= $Property->name; ?> en <?= empty($Property->area) ? $Property->city : $Property->area; ?> <br />

    <?= $Property->forsale ? "Se Vende" : "Se Alquila"; ?>

    <?php if( $Property->price ) : ?>
        por <?= number_format( $Property->price ); ?>
    <?php endif; ?>

    <?php if( $Property->mainimg ) : ?>
        <br />
        <a target='_blank' href='<?= $url; ?>'>
            <img src='<?= $Property->mainimg->get( 680 ); ?>' border='0' />
        </a>
    <?php endif; ?>
    <br /><a target='_blank' href='<?= $url; ?> '> Oprime aqu&iacute; para ver anuncio. </a>

</p>
<p style="font-weight: bold;"><?=t("Cliente que pregunta:");?></p>
<ul>
	<li><?= t('Nombre: ').$name; ?></li>
	<li><?= t('Email: ').$email; ?></li>
	<li><?= t('Tel.: ').$phone; ?></li>
	<li>
	    <?= t('Mensaje: ')?> <br /><br />
    	<?= nl2br( strip_tags($msg) ); ?>
	</li>

</ul>

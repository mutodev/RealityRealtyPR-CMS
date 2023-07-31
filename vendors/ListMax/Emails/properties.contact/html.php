<p style="font-weight: bold;">

    <?= $Property->name; ?> en <?= empty($Property->area) ? $Property->city : $Property->area; ?> <br />
    
    <?php if( $Property->mainimg ) : ?>    
        <br />
        <a target='_blank' href='<?= $url; ?>'>
            <img src='<?= $Property->mainimg->get( 660 ); ?>' border='0' style='border:1px solid gray;background:white;padding:5px;margin:10px 0' />
        </a>
    <?php endif; ?>
    <br />
    <a target='_blank' href='<?= $url; ?> '> Oprime aqu&iacute; para ver anuncio. </a>

</p>
<p style="font-weight: bold;"><?=t("Cliente que pregunta:");?></p>
<ul>
	<li><?= t('Nombre: ').$name; ?></li>
	<li><?= t('Email: ').$email; ?></li>
	<li><?= t('Tel.: ').$phone; ?></li>
	<li><?= t('Mensaje: ')?> 
	    <br /><br />
    	<?= nl2br( strip_tags($msg) ); ?>
	</li>

</ul>        

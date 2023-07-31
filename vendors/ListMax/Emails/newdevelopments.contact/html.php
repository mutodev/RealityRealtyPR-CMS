<h2><?= t('Un usuario de Vocero.com ha enviado una pregunta respecto al Proyecto Nuevo #').$newdev_id; ?></h2>

<h3><?= t('Usuario:'); ?></h3>

<ul>
  <li><?= t('Nombre: ').$name; ?></li>
  <li><?= t('Email: ').$email; ?></li>
  <li><?= t('Tel.: ').$phone; ?></li>
</ul>

<h3><?= t('Mensaje:'); ?></h3>
<p><?= $msg; ?></p>
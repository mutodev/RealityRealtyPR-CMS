<?php
    $BASE = url(" ");
    $BASE_PUBLIC = $BASE."/img/email"
?>
<div align="center">

    <table border="0" cellspacing="0" cellpadding="0" width="700">
     <tr>
      <td style="padding: 0">
        <img border="0" width="700" height=106" src="<?= $BASE_PUBLIC ?>/top.jpg" />
      </td>
     </tr>
     <tr>

      <td style="padding: 0px; height:18.75px" align="center">
        <strong>Propiedad</strong>
      </td>

     </tr>
     <tr>
      <td style="padding: 0px">
        <img border="0" width="700" height="11" src="<?= $BASE_PUBLIC ?>/footer.jpg" alt="" />
      </td>
     </tr>
     <tr style="height:75.0px">
      <td valign="top" style="border-left: solid #680000 1px; border-right:solid #680000 1px; padding: 7px; height:75.0px">

    <div style="text-align: center; font-weight: bold;">
      #<?= ($Property->internal_num) ? $Property->internal_num : $Property->id; ?> - <?= $Property->name; ?> en <?= empty($Property->area) ? $Property->city : $Property->area; ?>
    </div>
    <br />

    <table border="0" cellpadding="0" cellspacing="0" width="100%">
      <tr>
        <?php if( $Property->mainimg ) : ?>
          <td width="400">
              <a target='_blank' href='<?= $url; ?>'>
                  <img src='<?= $Property->mainimg->get( 380 ); ?>' border='0' />
              </a>
          </td>
        <?php endif; ?>

          <td valign="top" style="padding-top: 20px;">

            <ul style="margin-left: 12px; padding: 0px;">
                <li><?= $Property->forsale ? "Se Vende" : "Se Alquila"; ?><?php if( $Property->price ) : ?> - $<?= number_format( $Property->price ); ?><?php endif; ?></li>
                <li><?= implode( " > " , $Property->category_path ) ?></li>
                <li><?= empty($Property->area) ? $Property->city : $Property->area; ?></li>

                <?php if( !empty( $Property->rooms )) : ?>
                  <li>Cuartos: <?= $Property->rooms; ?></li>
                <?php endif; ?>

                <?php if( !empty( $Property->baths )) : ?>
                  <li>Ba&ntilde;os: <?= $Property->baths; ?></li>
                <?php endif; ?>

                <li><a target='_blank' href='<?= $url; ?>'>M&aacute;s Informaci&oacute;n</a></li>
            </ul>

              <br />
              <br />
              <strong>Contacto</strong><br />
              Oficina: <strong><?= $Property->office_location; ?></strong><br />

              <?php if( !empty( $Property->office_phone )) : ?>
                Tel&eacute;fono: <?= $Property->office_phone; ?><br />
              <?php endif; ?>

              <?php if( !empty( $Property->office_fax )) : ?>
                Fax: <?= $Property->office_fax; ?><br />
              <?php endif; ?>

              <?php if( !empty( $Property->office_email )) : ?>
                E-mail: <?= $Property->office_email; ?>
              <?php endif; ?>

          </td>
      </tr>
    </table>


    <br /><a target='_blank' href='<?= $url; ?>'> Oprime aqu&iacute; para ver anuncio. </a>


      </td>
     </tr>
     <tr>
      <td style="padding: 0px;">
        <img border="0" width="700" height="11" src="<?= $BASE_PUBLIC ?>/footer.jpg" alt="" />
      </td>
     </tr>
    </table>

</div>

<?php 
    $BASE = "http://listmax.com/public/img/email/referido/";
?>    
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    </head>
    <body bgcolor="#FFFFFF" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
    
        <table width="700" border="0" cellspacing="0" cellpadding="0" align="center">
              <tr>
                <td background="<?= $BASE; ?>r1_c1.jpg" width="700" height="104" valign='middle'>
                    &nbsp; &nbsp; <img src='<?= $BASE; ?>/logo.gif' />
                </td>
              </tr>
              <tr>
                <td><img src="<?= $BASE; ?>r2_c1.gif" width="700" height="9"></td>
              </tr>
              <tr>
                <td background="<?= $BASE; ?>r3_c1.gif" style="font-family:Arial, Helvetica, sans-serif;font-size:14px;font-weight:bold" align="center">
                    <?= @$SUBJECT; ?>
                </td>
              </tr>
              <tr>
                <td><img src="<?= $BASE; ?>r5_c1.gif" width="700" height="17"></td>
              </tr>
              <tr>
                <td background="<?= $BASE; ?>r3_c1.gif">
                    <table width="99%" border="0" cellspacing="2" cellpadding="2" align="center">
                      <tr>
                        <td style="	border-right-width: 1px; border-left-width: 1px; border-right-style: solid;	border-left-style: solid;	border-right-color: #086306; border-left-color: #086306;font-family:Arial, Helvetica, sans-serif;font-size:12px;">
                            <table width="97%" border="0" cellspacing="4" cellpadding="4" align="center" >
                              <tr>
                                <td><?= $CONTENT; ?></td>
                              </tr>
                            </table>
                        </td>
                      </tr>
                    </table>
                </td>
            </tr>
            <tr>
              <td><img src="<?= $BASE; ?>r8_c1.gif" width="700" height="26"></td>
            </tr>
        </table>
        <p>&nbsp;</p>
    </body>
</html>
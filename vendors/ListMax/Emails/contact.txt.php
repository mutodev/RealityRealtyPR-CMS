 Nombre:  <?= $name; ?> 
--- 
 Tel.:   <?= $phone; ?> 
--- 
 Email:  <?= $email; ?> 
--- 
 Mensaje: 
 <?= $msg; ?> 
--- 
Referente: <?= $Property->name; ?> <?= $Property->forsale ? "Se Vende" : "Se Alquila"; ?> <?= $Property->category; ?> <?= $Property->id; ?> en <?= empty($Property->area) ? $Property->city : $Property->area; ?> 
--- 
Listador: <?= $lister ?> ( <?= $listerEmail ?> )

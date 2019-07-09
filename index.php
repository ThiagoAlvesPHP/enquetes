<?php
  require 'class.php';
  $sql = new enquete();
  //PEGANDO O IP DO USUARIO
  $ip = $_SERVER["REMOTE_ADDR"];

  //REGISTRANDO ENQUETE
  if (!empty($_POST['enquete']) && !empty($_POST['validade'])) {
    $enquete = addslashes($_POST['enquete']);
    $validade = addslashes($_POST['validade']);

    if ($sql->setEnquete($enquete, $validade, $ip)) {
      /*header('Location: index.php');*/
    } else {

    }

  }
?>
<!DOCTYPE html>
<html>
<head>
  <title></title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1"> 
  <link rel="icon" href="assets/img/favicon.png" type="image/x-icon"/>

  <link rel="stylesheet" type="text/css" href="css/bootstrap.css">
  <link rel="stylesheet" href="js/datetime/jquery.datetimepicker.css">
  <script type="text/javascript" src="js/jquery.min.js"></script>
  <script src="js/datetime/jquery.datetimepicker.full.js"></script>
  <script type="text/javascript" src="js/script.js"></script>

  <script type="text/javascript">
    $(function(){
      $('.dt_hs').datetimepicker({format:'d/m/Y H:i:s',formatDate:'Y-m-d H:i:s'});
    });
  </script>
</head>
<body>

<div class="container">
  <div class="row">
    <div class="col-sm-4">
      <h1>Criar Enquete</h1>
      Você só pode registrar uma enquete!
      <hr>
      <form method="POST">
        <label>Enquete</label>
        <input type="text" name="enquete" class="form-control" autofocus="" autocomplete="off">
        <label>Validade</label>
        <input type="text" name="validade" class="form-control dt_hs">
        <br>
        <button class="btn btn-block btn-primary btn-lg">Registrar</button>
      </form>
    </div>
    <div class="col-sm-4">
      
    </div>
    <div class="col-sm-4">
      
    </div>
  </div>
</div>

</body>
</html>
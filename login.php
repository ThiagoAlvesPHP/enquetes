<?php
  require 'class.php';
  $sql = new enquete();

  if (!empty($_POST['senha'])) {
    $senha = addslashes($_POST['senha']);

    if ($sql->getLogin($senha)) {
      header('Location: index.php');
    } else {
      $alert = '<div class="alert alert-danger"><strong>Alerta</strong> Senha Invalida! Tente Novamente.</div>';
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
</head>
<body>

<div class="container">
  <div class="row">
    <div class="col-sm-4"></div>
    <div class="col-sm-4">

      <div class="well">
        <h1>Login</h1>
        <?php
        if (isset($alert)) {
          echo $alert;
        }
        ?>
        <hr>
        <form method="POST">
          <label>Senha</label>
          <input type="password" name="senha" class="form-control" autofocus="" autocomplete="off">
          <br>
          <button class="btn btn-block btn-success btn-lg">Logar</button>
        </form>
      </div>

    </div>
    <div class="col-sm-4"></div>
  </div>
</div>

</body>
</html>
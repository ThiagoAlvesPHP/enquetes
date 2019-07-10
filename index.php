<?php
  require 'class.php';
  if (!isset($_SESSION['lg']) && empty($_SESSION['lg'])) {
    header('Location: login.php');
  }
  $sql = new enquete();
  //PEGANDO O IP DO USUARIO
  $ip = $_SERVER["REMOTE_ADDR"];

  //PEGANDO ENQUETE DO USUARIO BASEADO NO IP DELE
  $enquete = $sql->getEnquete($ip);
  //PEGANDO AS OPÇÕES BASEADO NA ENQUETE REGISTRADA
  $opcoes = $sql->getOpcoes($enquete['id']);
  //PEGANDO TODAS AS ENQUETES
  $getEnquetes = $sql->getEnquetes();

  //REGISTRANDO ENQUETE
  if (!empty($_POST['enquete']) && !empty($_POST['validade'])) {
    $enquetes = addslashes($_POST['enquete']);

    $dt = explode('/', addslashes($_POST['validade']));
    $dt2 = explode(' ', $dt[2]);
    $validade = $dt2[0].'-'.$dt[1].'-'.$dt[0].' '.$dt2[1];

    //VERIFICANDO SE A DATA E HORARIO DEFINIDOS ESTÃO IGUAIS OU ABAIXO DA DATA E HORARIO ATUAIS
    if (date('Y-m-d H:i:s') >= $validade) {
      echo '<script>alert("Data e Horario abaixo da data e horario atual!");</script>';
      echo '<script>window.location.href="index.php";</script>';
    } else {
      if ($sql->setEnquete($enquetes, $validade, $ip)) {
        echo '<script>alert("Enquete realizada com sucesso!");</script>';
        echo '<script>window.location.href="index.php";</script>';
      } else {
        echo '<script>alert("Você só pode fazer um registro!");</script>';
        echo '<script>window.location.href="index.php";</script>';
      }
    }

  } else {
    if (isset($_POST['enquete']) && isset($_POST['validade'])) {
      echo '<script>alert("Preencha os campos corretamente!");</script>';
      echo '<script>window.location.href="index.php";</script>';
    }
  }

  //REGISTRANDO OPÇÕES
  if (!empty($_POST['opcao01']) && !empty($_POST['opcao02'])) {
    $opcao01 = addslashes($_POST['opcao01']);
    $opcao02 = addslashes($_POST['opcao02']);

    $sql->setOpcoes($enquete['id'], $opcao01, $opcao02);

    echo '<script>alert("Opções registradas com sucesso!");</script>'; 
    echo '<script>window.location.href="index.php";</script>';

  } else {
    if (isset($_POST['opcao01']) && isset($_POST['opcao02'])) {
      echo '<script>alert("Preencha os campos corretamente!");</script>';
      echo '<script>window.location.href="index.php";</script>';
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
  <style type="text/css">
    .opcoes{
      text-align: center;
      font-size: 20px;
    }
  </style>
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
      Você só pode registrar uma enquete! - <a class="btn btn-danger" href="sair.php">Sair</a>
      <hr>
      <form method="POST">
        <label>Enquete</label>
        <input type="text" name="enquete" class="form-control" autocomplete="off">
        <label>Validade</label>
        <input type="text" name="validade" class="form-control dt_hs" autocomplete="off">
        <br>
        <button class="btn btn-block btn-primary btn-lg">Registrar Enquete</button>
      </form>
    </div>
    <div class="col-sm-4">
      <h1>Minhas Enquetes</h1>
      <hr>
      <div class="well text-center">
        <!-- VERIFICA SE EXISTEM OPÇÕES REGISTRADAS -->
        <?php if (!empty($opcoes)): ?>
          <div class="row">
            <label><?=htmlspecialchars($enquete['enquete']); ?></label><br>
            <?php foreach($opcoes as $op): ?>
              <div class="col-sm-6">
                <input type="text" value="<?=$op['opcao']; ?>" class="form-control opcoes" readonly="">
              </div>
            <?php endforeach; ?>
          </div>
        <?php else: ?>
          <!-- SE NÃO EXISTIR OPÇÕES REGISTRADAS 
          VERIFICAR SE EXISTE ENQUETE REGISTRADA -->
          <?php if(!empty($enquete)): ?>
            <label>Enquete</label><br>
            <?=htmlspecialchars($enquete['enquete']); ?><br>
            <label>Validade</label><br>
            <?=date('d/m/Y H:i:s', strtotime($enquete['validade'])); ?>
            <hr>
            <form method="POST">
              <label>Opção 01</label>
              <input type="text" name="opcao01" class="form-control" required="">
              <label>Opção 02</label>
              <input type="text" name="opcao02" class="form-control" required="">
              <br>
              <button class="btn btn-success btn-lg btn-block">Registrar</button>
            </form>

          <!-- NÃO EXISTINDO ENQUETE REGISTRADA IMPRIME UM ALERTA -->
          <?php else: ?>
            <div class="alert alert-info">Não existe enquete registrada!</div>
          <?php endif; ?>

        <?php endif; ?>
      </div>
    </div>
    <div class="col-sm-4">
      <h1>Enquetes Registradas</h1>
      <hr>
      <div class="row text-center">
      <!-- SE EXISTIR ENQUETES PREENCHIDAS -->
      <?php if(!empty($getEnquetes)): ?>
        <?php foreach($getEnquetes as $op): ?>
          <?php $OpcoesEnq = $sql->getOpcoes($op['id']); ?>
          <?php if(!empty($OpcoesEnq)): ?>
            <?php echo htmlspecialchars($op['enquete']); 

            $contador = $sql->getContador($op['validade']);
            ?>
            <script type="text/javascript">
                $(document).ready(function(){
                    // Requisicao AJAX
                    var requisicao = function(){
                        $.ajax({
                            url: <?=$contador; ?>
                        }).done(function(resultado){
                          console.log(resultado);
                            // Exibe o resultado no elemento com ID contador
                            $("#contador").html(resultado);
                        });
                    };
                    
                    // Executa a requisicao com intervalo de 100ms
                    setInterval(requisicao, 100);
                });             
            </script>
            <br>        
            <?php foreach ($OpcoesEnq as $o): ?>
              <div class="col-sm-6">
                <button class="btn btn-block btn-info"><?=$o['opcao']; ?></button>
              </div>
            <?php endforeach; ?>
            <hr>
          <?php endif; ?>
        <?php endforeach; ?>
      <?php endif; ?>
      </div>
    </div>
  </div>
</div>

</body>
</html>
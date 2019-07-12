<?php
  require 'class.php';
  $sql = new enquete();
  //PEGANDO O IP DO USUARIO
  $ip = $_SERVER["REMOTE_ADDR"];

  //CRIANDO PAGINAÇÃO
  $limite = 10;
  $pg = 1;
  if (isset($_GET['p']) && !empty($_GET['p'])) {
    $pg = addslashes($_GET['p']);
  }
  $p = ($pg - 1) * 10;

  //PEGANDO TODAS AS ENQUETES
  $getEnquetes = $sql->getEnquetes($p, $limite);

  //ULTIMA ENQUETE REGISTRADA
  $enqueteUltima = $sql->getEnqueteUltima();


  //VOTANDO
  if (isset($_GET['voto']) && !empty($_GET['voto'])) {
    if ($sql->setVoto(addslashes($_GET['voto']), $ip)) {
      echo '<script>alert("Você acabou de votar!");</script>';
      echo '<script>window.location.href="login.php";</script>';
    } else {
      echo '<script>alert("Você já votou!");</script>';
      echo '<script>window.location.href="login.php";</script>';
    }
  }

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
    <div class="col-sm-4">

      <div class="well">
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
    
    <div class="col-sm-8">
      <h1>Ultima Enquete</h1>

      <div class="well text-center">
        <?php $OpcoesEnq = $sql->getOpcoes($enqueteUltima['id']); ?>
        <?php if(!empty($OpcoesEnq)): ?>
          <?php if ($enqueteUltima['validade'] > date('Y-m-d H:i:s')): ?>
            <h3><?=htmlspecialchars($enqueteUltima['enquete']); ?></h3>
            <input type="text" id="id" hidden="" value="<?=$enqueteUltima['validade']; ?>">
            <div id="cronometro"></div>

            <h3>Vote Agora</h3>
            <div class="row">
            <?php foreach ($OpcoesEnq as $o): 
              if ($enqueteUltima['validade'] > date('Y-m-d H:i:s')):
                $c = $sql->countVotos($o['id']);
                ?>
                  <div class="col-sm-6">
                    <a href="?voto=<?=$o['id']; ?>" class="btn btn-block btn-info"><?=$o['opcao']; ?> - 
                    <?php
                    if ($c['count'] > 0) {
                      if ($c['count'] == 1) {
                        echo $c['count'].' Voto';
                      } else {
                        echo $c['count'].' Votos';
                      }
                    } else {
                      echo $c['count'].' Votos';
                    }
                    ?>
                    </a>
                  </div>
                <?php
              endif;
            endforeach; ?>
            </div>

          <?php else: ?>
            <div class="alert alert-danger"><strong>VENCIDO</strong></div>
          <?php endif; ?>
        <?php else: ?>
          <div class="alert alert-danger"><strong>NÃO EXISTEM OPÇÕES DE VOTO REGISTRADOS</strong></div>
        <?php endif; ?>
        
      </div>

      <hr>

      <h3>Todas as Enquetes</h3>
      <div class="row text-center">
      <!-- SE EXISTIR ENQUETES PREENCHIDAS -->
      <?php if(!empty($getEnquetes)): ?>
        <?php foreach($getEnquetes as $op): ?>
          <?php 
          $OpcoesEnq = $sql->getOpcoes($op['id']); 

          ?>
          <?php if(!empty($OpcoesEnq)): ?>
            <div class="row">
              <div class="col-sm-12">
                <?php 
                echo htmlspecialchars($op['enquete']); 
                $contador = $sql->getContadorIndex($op['validade']);

                if ($op['validade'] > date('Y-m-d H:i:s')) {
                  if ($contador[0] > 1) {
                    echo ' / Faltam: '.$contador[0].' Dias e '.$contador[1].' horas';
                    echo '<a href="enquete.php?id='.$op['id'].'" class="btn btn-block btn-successs"><i>Ver Completo...</i></a>';

                  } else {
                    if ($contador[0] == 1) {
                      echo ' / Falta: '.$contador[0].' Dia e '.$contador[1].' horas';
                      echo '<a href="enquete.php?id='.$op['id'].'" class="btn btn-block btn-successs"><i>Ver Completo...</i></a>';
                    } else {
                      echo ' / Faltam: '.$contador[0].' Dias e '.$contador[1].' horas';
                      echo '<a href="enquete.php?id='.$op['id'].'" class="btn btn-block btn-successs"><i>Ver Completo...</i></a>';
                    }
                  } 
                } else {
                  echo '<div class="alert alert-danger"><strong>VENCIDO</strong></div>';
                }

                ?>
                
              </div>
            </div>
            <hr>
          <?php endif; ?>
        <?php endforeach; ?>
      <?php endif; ?>
      </div>


      <?php
      $paginas = $sql->countEnquetes();
      $paginas = $paginas['count'] / 10;
      ?>
       <!-- PAGINAÇÃO -->
        <nav aria-label="Navegação de página exemplo">
          <ul class="pagination">
            <?php
                if ($pg == 1) {
                    echo '<li class="page-item disabled">
                      <span class="page-link">Anterior</span>
                    </li>';
                    for ($i=0; $i < $paginas; $i++) { 
                        if ($i < $pg+2) {
                            echo '
                            <li class="page-item">
                                <a class="page-link" href="?p='.($i+1).'">'.($i+1  ).'</a>
                            </li>';
                        }
                    }
                    echo '
                            <li class="page-item">
                                <a class="page-link">...</a>
                            </li>';
                    echo '
                            <li class="page-item">
                                <a class="page-link" href="?p='.($i+1).'">'.($i+1  ).'</a>
                            </li>';

                    echo '<li class="page-item"><a class="page-link" href="?p='.($pg+1).'">Próximo</a></li>';
                } else {
                    echo '<li class="page-item"><a class="page-link" href="?p='.($pg-1).'">Anterior</a></li>';
                    for ($i=0; $i < $paginas; $i++) {

                        if ($i < $_GET['p']+2 && $i > $_GET['p']-2) {
                            echo '
                            <li class="page-item">
                                <a class="page-link" href="?p='.($i+1).'">'.($i+1  ).'</a>
                            </li>';
                        }
                    }
                    echo '
                            <li class="page-item">
                                <a class="page-link">...</a>
                            </li>';
                    echo '
                            <li class="page-item">
                                <a class="page-link" href="?p='.($i+1).'">'.($i+1  ).'</a>
                            </li>';

                    echo '<li class="page-item"><a class="page-link" href="?p='.($pg+1).'">Próximo</a></li>';
                } 
            ?>
          </ul>
        </nav> 


    </div>
  </div>
</div>

<script type="text/javascript">
  $(function(){

    //NOTIFICAÇÕES DE MENSAGENS
    function Contador() {
      var id = $('#id').val();

      $.ajax({
        url:'ajax.php',
        type:'POST',
        data:{ id:id },
        success:function(json) {
          $('#cronometro').html(json);              
        }
      });
    }

    $(function(){
      setInterval(Contador, 100);
      Contador();
    });

  });
</script>

</body>
</html>
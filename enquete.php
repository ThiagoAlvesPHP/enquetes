<?php
require 'class.php';
if(isset($_GET['id']) && !empty($_GET['id'])): 
$id = addslashes($_GET['id']);

$sql = new enquete();

$enquete = $sql->getEnqueteId($id);
if (!empty($enquete)) {
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
		<div class="col-sm-12 text-center">
			<h1><?=htmlspecialchars($enquete['enquete']); ?></h1>
		</div>
	</div>
</div>

</body>
</html>

<?php
} else {
	?>
	<script type="text/javascript">
		alert('Nenhuma enquete encontrada');
		window.location.href='login.php';
	</script>
	<?php
}

?>

<?php else: ?>
	<script type="text/javascript">
		alert('Nenhuma enquete encontrada');
		window.location.href='login.php';
	</script>
<?php endif; ?>
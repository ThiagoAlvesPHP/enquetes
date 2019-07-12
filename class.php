<?php
session_start();
date_default_timezone_set('America/Sao_Paulo');
class enquete{
	private $db;
	
	public function __construct(){
		$file = file_get_contents('config.json');
		$options = json_decode($file, true);

		$config = array();

		$config['db'] = $options['db'];
		$config['host'] = $options['localhost'];
		$config['user'] = $options['user'];
		$config['pass'] = $options['pass'];

		try {
			$this->db = new PDO("mysql:dbname=".$config['db'].";host=".$config['host']."", "".$config['user']."", "".$config['pass']."");
		} catch(PDOException $e) {
			echo "FALHA: ".$e->getMessage();
		}
	}

	//LOGAR
	public function getLogin($senha){

		$sql = $this->db->prepare("SELECT * FROM usuarios WHERE senha = :senha");
		$sql->bindValue(':senha', md5($senha));
		$sql->execute();

		if ($sql->rowCount() > 0) {

			$dados = $sql->fetch(PDO::FETCH_ASSOC);
			$_SESSION['lg'] = $dados;

			return true;
		} else {
			return false;
		}
	}

	//INSERIR AGENDA
	public function setEnquete($enquete, $validade, $ip){		
		$sql = $this->db->prepare('SELECT * FROM enquetes WHERE ip = :ip');
		$sql->bindValue(':ip', $ip);
		$sql->execute();

		if ($sql->rowCount() == 0) {
			$sql = $this->db->prepare('
				INSERT INTO enquetes 
				SET enquete = :enquete,
				validade = :validade,
				ip = :ip');
			$sql->bindValue(':enquete', $enquete);
			$sql->bindValue(':validade', $validade);
			$sql->bindValue(':ip', $ip);
			$sql->execute();

			return true;
		} else {
			return false;
		}
	}

	public function getEnquete($ip){
		$sql = $this->db->prepare('SELECT * FROM enquetes WHERE ip = :ip');
		$sql->bindValue(':ip', $ip);
		$sql->execute();

		return $sql->fetch(PDO::FETCH_ASSOC);
	}

	public function getEnqueteId($id){
		$sql = $this->db->prepare('SELECT * FROM enquetes WHERE id = :id');
		$sql->bindValue(':id', $id);
		$sql->execute();

		return $sql->fetch(PDO::FETCH_ASSOC);
	}

	public function getEnquetes($p, $limite){	
		$sql = $this->db->prepare("SELECT * FROM enquetes LIMIT ".$p.", ".$limite."");
		$sql->execute();

		return $sql->fetchAll(PDO::FETCH_ASSOC);
	}

	public function countEnquetes(){
		$sql = $this->db->prepare("SELECT COUNT(*) as count FROM enquetes");
		$sql->execute();

		return $sql->fetch(PDO::FETCH_ASSOC);
	}

	public function setOpcoes($id_enquete, $opcao01, $opcao02){
		$array = array($opcao01, $opcao02);

		foreach ($array as $opcao) {
			$sql = $this->db->prepare('
				INSERT INTO opcoes 
				SET 
				id_enquete = :id_enquete, 
				opcao = :opcao');
			$sql->bindValue(':id_enquete', $id_enquete);
			$sql->bindValue(':opcao', $opcao);
			$sql->execute();
		}
		return true;
	}

	public function getOpcoes($id_enquete){
		$sql = $this->db->prepare('SELECT * FROM opcoes WHERE id_enquete = :id_enquete');
		$sql->bindValue(':id_enquete', $id_enquete);
		$sql->execute();

		return $sql->fetchAll(PDO::FETCH_ASSOC);
	}

	public function getContador($validacao){
		// Define as datas
		$data_atual = date('d-m-Y h:i:s');
		// Converte as datas para a hora UNIX e realiza o calculo da diferenca
		$diferenca  = strtotime($validacao) - strtotime($data_atual);
		/*86400 quantidade de segundos tem o dia*/
		$dias = intval($diferenca / 86400);
		$marcador = $diferenca % 86400;
		/*3600 é a quantidade de segundos que tem uma hora*/
		$horas = intval($marcador/3600);

		$marcador = $marcador % 3600;
		$minutos = intval($marcador / 60);
		$segundos = $marcador % 60;

		

		if ($diferenca > 0):
			ob_start();
			?>
			<div class="table-responsive" style="text-align: center;"><table class="table"><thead><tr><th width="25%">Dias</th><th width="25%">Horas</th><th width="25%">Minutos</th><th width="25%">Segundos</th></tr></thead><tbody><tr><td><button class="btn btn-primary btn-block"><?=$dias; ?></button></td><td><button class="btn btn-primary btn-block"><?=$horas; ?></button></td><td><button class="btn btn-primary btn-block"><?=$minutos; ?></button></td><td><button class="btn btn-primary btn-block"><?=$segundos; ?></button></td></tr></tbody></table></div><?php
			$html = ob_get_contents();
			ob_end_clean();
			return $html;
		else:
			return '<div class="alert alert-danger">Vencido</div>';
		endif;
	}

	public function getContadorIndex($validacao){
		// Define as datas
		$data_atual = date('d-m-Y h:i:s');
		// Converte as datas para a hora UNIX e realiza o calculo da diferenca
		$diferenca  = strtotime($validacao) - strtotime($data_atual);
		/*86400 quantidade de segundos tem o dia*/
		$dias = intval($diferenca / 86400);
		$marcador = $diferenca % 86400;
		/*3600 é a quantidade de segundos que tem uma hora*/
		$horas = intval($marcador / 3600);
		$marcador = $marcador % 3600;
		$minutos = intval($marcador / 60);
		$segundos = $marcador % 60;

		if ($diferenca > 0):
			return array($dias, $horas, $minutos, $segundos);
		else:
			return '<div class="alert alert-danger">Vencido</div>';
		endif;
	}

	public function getEnqueteUltima(){
		$sql = $this->db->prepare('SELECT * FROM enquetes ORDER BY id DESC LIMIT 1');
		$sql->execute();

		return $sql->fetch(PDO::FETCH_ASSOC);
	}

	public function setVoto($id_opcao, $ip){

		$sql = $this->db->prepare('SELECT * FROM votos WHERE ip = :ip');
		$sql->bindValue(':ip', $ip);
		$sql->execute();

		if ($sql->rowCount() == 0) {
			$sql = $this->db->prepare('
				INSERT INTO votos 
				SET id_opcao = :id_opcao,
				ip = :ip');
			$sql->bindValue(':id_opcao', $id_opcao);
			$sql->bindValue(':ip', $ip);
			$sql->execute();

			return true;
		} else {
			return false;
		}

	}

	public function countVotos($id_opcao){
		$sql = $this->db->prepare("
			SELECT COUNT(*) as count 
			FROM votos 
			WHERE id_opcao = :id_opcao");
		$sql->bindValue(':id_opcao', $id_opcao);
		$sql->execute();

		return $sql->fetch(PDO::FETCH_ASSOC);
	}
	
}
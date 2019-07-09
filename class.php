<?php
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

	

	//INSERIR AGENDA
	public function setEnquete($enquete, $validade, $ip){		
		$sql = $this->db->prepare('SELECT * FROM enquetes WHERE ip = :ip');
		$sql->bindValue(':ip', $ip);
		$sql->execute();

		$dt = explode('/', $validade);
		$dt2 = explode(' ', $dt[2]);
		$validade = $dt2[0].'-'.$dt[1].'-'.$dt[0].' '.$dt2[1];

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
	
}
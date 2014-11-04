<?php
	


	function MongoConnect(){
		date_default_timezone_set('America/Sao_Paulo');
		global $collectionLeituras;
		global $collectionEnchentes;
		
		$con = new MongoClient("mongodb://localhost/mydb");
		$db = $con->selectDB("mydb");
		
		$collectionEnchentes = new MongoCollection($db, 'enchentes');
		
		$collectionLeituras = new MongoCollection($db, 'leituras');
	}
		
	if (isset($_GET["getLeituras"])) {
		header('content-type: application/json; charset=utf-8');
		header("access-control-allow-origin: *");
		
		MongoConnect();
		
		echo $_GET["getLeituras"].' ('.json_encode(getLeituras($_GET['qtdLeituras'], true)).')';        
    }
	
	if (isset($_GET['getEstadoAlerta'])) {
		header('content-type: application/json; charset=utf-8');
		header("access-control-allow-origin: *");	
		
		MongoConnect();
		
        echo $_GET["getEstadoAlerta"].' ('.json_encode(getEstadoAlerta()).")";
    }
	
	if (isset($_GET['getEnchentes'])) {
        header('content-type: application/json; charset=utf-8');
        header("access-control-allow-origin: *");   
		
		MongoConnect();
		
        echo $_GET["getEnchentes"].' ('.json_encode(getEnchentes()).")";
    }

	function getEnchentes () {
		global $collectionEnchentes;
		
		$array = array();
		$i = 0;
		foreach ($collectionEnchentes as $obj) {
			$array[$i][0] = $obj['Data'];
			$array[$i][1] =  $obj['NivelRio'];
			$i++;
		}
		
		return $array;		
	}
	
	/**
	 * Verifica nivel do rio na ultima leitura e avisos no site da Defesa Civil de SC
	 * 
	 * @return array[2]
	 * @return [0] = Nivel do Rio: 0- Normal, 1- Alerta, 2- Inundação
	 * @return [1] = Defesa Civil: 0- Nada encontrado, 1- Aviso encontrado
	 */
	 
	function getEstadoAlerta() {
		global $collectionLeituras;
		
		/**
		 * Verifica avisos no site da Defesa Civil de SC e busca por palavras chave
		 * 
		 * @return boolean: true= avisos encontrados, false= nada encontrado
		 * @see http://simplehtmldom.sourceforge.net/manual.htm 
		 */
		function verificarEstadoDefesaCivil() {
			
			#Converte erros em exceções
			function exception_error_handler($errno, $errstr, $errfile, $errline) {
				throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
			}
			set_error_handler("exception_error_handler");
			
			try {
				#Palavras chave de pesquisa
				$palavrasChave = array('Timbó', 'Blumenau', 'médio vale');
				
				#Busca quadr de informações no site da defesa civil
				#include "./Comum/php/lib/simple_html_dom.php";
				include "lib/simple_html_dom.php";
				$html = file_get_html('http://www.defesacivil.sc.gov.br/');
				
				#Filtra dados			
				$html =  $html -> getElementById("user5");
				$html = $html -> find("p");
				
				#Varre dados em busca de palavras chave
				$cont = 0;
				$achouSemAviso = false;
				$achouPalavra = false;
				foreach ($html as $key => $value) {
					if ($cont != 0) {
						
						#Procura por "Sem aviso."
						if (strpos($value,'Sem aviso.') !== false) {
						    $achouSemAviso = true;
						}
						
						#Procura por palavras Chave
						foreach ($palavrasChave as $key => $palavraChave) {
							if (strpos($value, $palavraChave) !== false) {
							    $achouPalavra = true;
							}
						}	
					}
					$cont++;
				}
				
				#Se não achou "Sem aviso" e achou palavra chave
				if (!$achouSemAviso & $achouPalavra) {
					return true;
				} else {
					return false;
				}
			} catch (Exception $e) {
				#echo 'Cuaght exception: ', $e -> getMessage(), "\n";
				return false;
			}
			
		}
		
		#Define constantes
		$nivelRioAlerta = 8;
		$nivelRioInundacao = 10;

		#Busca ultimo dado valido
		$query = array('nivelRio' => array('$ne' => 'null'));
		$cursor = $collectionLeituras -> find($query);
		$cursor -> sort(array('dataHora' => -1));
		$cursor -> limit(1);
	
		foreach ($cursor as $document) {
			$nivelRio = $document["nivelRio"];
		}
		
		#Avalia resultados
		$retorno = array('0','0');
		
		if (($nivelRio >= $nivelRioAlerta) & ($nivelRio < $nivelRioInundacao)) {
			$retorno['0'] = '1';
		} else if ($nivelRio >= $nivelRioInundacao) {
			$retorno['1'] = '2';
		}
		if (verificarEstadoDefesaCivil()) {
			$retorno['1'] = '1';
		}
		
		return $retorno;

	}

	/**
	 * Busca a quantidade definida de leituras no banco de dados
	 * 
	 * @param qtdLeituras = quantidade de leituras a retornar
	 * @param leiturasValidas = se true, retorna apenas leituras validas (nuvel <> 'null')
	 * @return array[][] = 0: numero da leitura, 1= valor (0-dataHora, 1-nivelRio, 2-nivelChuva)
	 */
	function getLeituras($qtdLeituras, $leiturasValidas = true) {
		global $collectionLeituras;
		
		#Busca ultimas leituras
		if ($leiturasValidas) {
			$query = array('nivelRio' => array('$ne' => 'null'));
			$cursor = $collectionLeituras -> find($query);
		} else {
			$cursor = $collectionLeituras -> find();
		}
		$cursor -> sort(array('dataHora' => -1));
		$cursor -> limit($qtdLeituras);

		#Insere leituras no array
		$leituras = array();
		$i = '1';
		foreach ($cursor as $document) {
			$Hora = date(DATE_ISO8601, $document["dataHora"] -> sec);
			
			$leituras[$i]['0'] = date("d/m", strtotime($Hora));
			$leituras[$i]['1'] = date("H:i", strtotime($Hora));
			$leituras[$i]['2'] = strval($document["nivelRio"]);
			switch ($document["nivelChuva"]) {
				case 0:	
					$leituras[$i]['3'] = 'Nula';
					break;
				case 1:
					$leituras[$i]['3'] = 'Moderada';
					break;
				case 2:
					$leituras[$i]['3'] = 'Forte';
					break;
				default:
					$leituras[$i]['3'] = 'null';
					break;
			}
			
			$i++;
		}

		
		return $leituras;
	}
?>
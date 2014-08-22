function phpWebServiceAcesso()
{
	<?php
		echo "TESTE PHP";
		$client = new SoapClient("http://servidor/services/ServiceWS?wsdl"); // Edereço do servidor a ser consumido (lido)
		try {
		  $obj = $client->recuperarDados(array("idproduto" => "5")); //Neste exemplo eu estou chamando a função do servidor chamada recuperarDados e passando o numero 5 para a variavel idproduto
		  if ($myxml = simplexml_load_string ($obj -> return)) {
		    foreach ($myxml as $post) { //Loop para mostrar os dados
		    
		    }
		   } else 
		    { echo 'Erro ao ler ficheiro XML'; }
		  
		} catch (Exception $e) {
		  echo "ERRO: " . $e->getMessage();
		}
	?>
  alert("teste");
}
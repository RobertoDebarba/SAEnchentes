<!DOCTYPE html>
<!--
    Autor: jonathan
    
    sobre as classes utilizadas acesse
    http://bootstrapdocs.com/v3.2.0/docs/
-->
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="Sistema de monitoramento de Enchentes de Timbó">
        <meta name="author" content="Jonathan, Luan e Roberto">
         <link rel="icon" href="icone da barinha">
        
        <title>SAEnchentes Timbó</title>
        
        <!-- Bootstrap -->
        <link href="./_css/bootstrap.css" rel="stylesheet">
        <link href="./_css/bootstrap-theme.min.css" rel="stylesheet">       
        <link href="./_css/Styles.css" rel="stylesheet" />
        <!--fim carregamento bootstrap -->
        
        <script src="https://maps.googleapis.com/maps/api/js?v=3.exp"></script>
        
        <script type="text/javascript" src="./_js/mapa.js"></script>
        <script type="text/javascript" src="./_js/ajax.js"></script>
        
        <!-- Just for debugging purposes. Don't actually copy these 2 lines! -->
        <!--[if lt IE 9]><script src="./js/ie8-responsive-file-warning.js"></script><![endif]-->
        <script src="./_js/ie-emulation-modes-warning.js"></script>
    
        <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
        <script src="./_js/ie10-viewport-bug-workaround.js"></script>

        
        <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
          <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
                
    </head>
    <body>
        
        <header class="navbar navbar-default navbar-fixed-top" >
            <div class="container-fluid">
                 <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>           
                    <a class="navbar-brand" href="#">SAEnchentes</a>    
                    
                 </div>                         
                <nav class="navbar-collapse collapse">
                    <ul class="nav navbar-nav">
                        <li class="active"><a href="#">Inicio</a></li>
                        <li><a href="#">Monitoramentos</a></li>
                        <li><a href="#Sobre">Sobre</a></li>
                    </ul>
                </nav>
            </div>
        </header>

        <div class="container-fluid">
            <div id="painel1" class="col-lg-8">
                <div id="mapa">
                </div>
            </div>
            
            <div id="painel2" class="col-lg-4">
                <div class="alert alert-success" id="alerta">
                    <img src="_Imagens/ok2.png" id="alertaImagem"/>
                    <strong>Status: </strong> Rio em condições normais.
                </div>
                <div id="clima">
                    <iframe src="http://selos.climatempo.com.br/selos/MostraSelo120.php?CODCIDADE=2070&SKIN=padrao" id="climatempo">
                    </iframe>
                    <!-- Widget Previs&atilde;o de Tempo CPTEC/INPE -->
                    <iframe allowtransparency="true" marginwidth="0" marginheight="0" hspace="0" vspace="0" frameborder="0" scrolling="no" 
                        src="http://www.cptec.inpe.br/widget/widget.php?p=5400&w=h&c=748ccc&f=ffffff" height="200px" width="215px">
                    </iframe>
                    <!-- Widget Previs&atilde;o de Tempo CPTEC/INPE -->
                </div>
            </div>
        </div>
        
        <div class="container-fluid">
            <div id="painel3" class="col-lg-4">
                
                 <input type="button" name="botao" value="Dispara função" onclick="teste()">                
            </div>
        </div>
        <p id="resposta"></p>
        <button name="botao" id="botao">Marcar Lido</button>
        
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>

        <script type="text/javascript">
        
        $(document).ready(function(){
            
            $("#botao").click(function(){
                
                var url = "./_php/webServiceAcesso.php";
                
                var data = $("#botao").attr("id");
                
                $.post(url, function(result) {
                    $("#resposta").html("Resultado do PHP: " + result); // Só pra verificar retorno
                });
                
            });
                
        });
        
        </script>
        <script src="./_js/jquery.min.js"></script>
        <script src="./_js/bootstrap.min.js"></script> 
    </body>
    
</html>
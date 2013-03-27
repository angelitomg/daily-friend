<?php
header('X-Frame-Options: GOFORIT'); 
header('p3p: CP="ALL DSP COR PSAa PSDa OUR NOR ONL UNI COM NAV"');
	
	/**
	* 
	* @App: DailyFriend
	*
	* @Description: Aplicativo que sorteia um amigo aleatorio, posta uma mensagem
	* no mural do usuario e se o usuario desejar, envia uma mensagem para o amigo
	* sorteado.
	*
	* @Author: Angelito M. Goulart 
	*		  http://angelitomg.com
	*
	* @Date: 22/06/2012
	*
	*/
	
	/* Definicao do charset */
	ini_set('default_charset', 'UTF-8');
	
	/* Classes necessarias */
	require('src/facebook.php');
	require('src/daily_friend.php');
	
	/* Criacao do objeto */
	$dailyFriend = new DailyFriend();
	
	/* Obtem o amigo aleatorio */
	$amigo = $dailyFriend->obterAmigo();	

	/* Se o form for submetido, pega os dados da mensagem e envia para o usuario */
	if (isset($_POST['enviar_mensagem'])){
		$mensagem = (isset($_POST['mensagem'])) ? $_POST['mensagem'] : '';
		$resultado = $dailyFriend->enviarMensagem($mensagem);
	} 
		
?>

<html>

<head>

<link rel="stylesheet" type="text/css" href="css/estilo.css" />
<script type="text/javascript" src="js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="js/principal.js"></script>

<meta property="fb:app_id" content="383060791756185" />
<meta property="og:title" content="Daily Friend"/>
<meta property="og:type" content="website" />
<meta property="og:url" content="http://angelitomg.com/daily-friend/"/>

</head>

<title>Daily Friend</title>

<body>

<?php if(!empty($amigo)): ?>
	
	<div id="principal">
	
		<!-- Box de exibicao do amigo -->
		<div id="box-amigo">
		
			<!-- Titulo do box -->
			<div id="titulo">Seu amigo do dia é... </div>
			
			<!-- Dados do amigo -->
			<div id="amigo">
				<img alt="imagem_amigo" src="https://graph.facebook.com/<?php echo $amigo['id']; ?>/picture" />
				<div>
					<a target="_top" href="http://facebook.com/<?php echo $amigo['id']; ?>">
						<?php echo $amigo['name']; ?>
					</a>
				</div>
			</div>
			
			<!-- Div com botoes de acao -->
			<?php /*
			<div id="botoes">
			
				<!-- Botao enviar mensagem -->
				<input class="botao-facebook" type="button" id="botao-mensagem" value="Publicar no Mural" />
				
			</div>
			
			
			<?php if(isset($resultado)): ?>
				<!-- Resultado do envio da mensagem -->
				<div id="resultado"><?php echo $resultado; ?></div>			
			<?php endif; ?>

			<!-- Div com textarea para envio da mensagem -->
			<div id="div-mensagem">
				<form name="mensagem" action="index.php" method="post">
					<textarea name="mensagem" id="mensagem" cols="40" rows="4"></textarea>
					<p><input class="botao-facebook" type="submit" name="enviar_mensagem" value="Enviar" /></p>
				</form>
			</div>
			
			<!-- Botao Recomendar -->
			<div id="botao-recomendar">
				<iframe src="http://www.facebook.com/plugins/like.php?href=<?php echo $appUrl; ?>&layout=standard&show_faces=false&width=380&action=recommend&colorscheme=light&height=25&locale=pt_BR" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:250px; height:50px;" allowTransparency="true"></iframe>
			</div>
			*/ ?>
			
		</div>
		
	</div>
	
<?php else: ?>

	<script type="text/javascript">
		location.href= "http://facebook.com";
	</script>
	
<?php endif; ?>

</body>
</html>


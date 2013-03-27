<?php
	
/**
*
*
* @class: DailyFriend
*
* @description: Classe principal do aplicativo para Facebook DailyFriend
*
* @author: Angelito M. Goulart < http://facebook.com/angelito.goulart >
*
* @date: 22/06/2012
*
*
*/

class DailyFriend{
	
	/**
	* Atributos da classe
	* appId -> ID da aplicacao
	* secret -> Chave secreta da aplicacao
	* dbPath -> Caminho do banco de dados
	* appUrl -> URL do aplicativo
	*/
	private $appId 	= '383060791756185';
	private $secret = '';
	private $dbPath = 'db/db.sqlite';
	private $appUrl = 'http://apps.facebook.com/383060791756185/';
	private $conexao;
	private $usuario;
	private $facebook;

	/**
	* Construtor
	*/
	function __construct(){

	
		/**
		* Cria o objeto da class Facebook para acesso a API
		*/
		$this->facebook = new Facebook(array(
			'appId' => $this->appId,
			'secret' => $this->secret));
		
			
		/**
		* Conexao com banco de dados
		*/
		$this->conectar();
		
		
		/**
		* Verifica a autenticacao e as permissoes do usuario
		*/
		try {
		    
		   	// Verifica se o usuario esta autenticado
		    $user_profile = $this->facebook->api('/me');
		    $this->usuario = $this->facebook->getUser();

		} catch (FacebookApiException $e) {
		    //header("location: " . $this->facebook->getLoginUrl($params = array('scope' => "publish_stream")));
		    $url = $this->facebook->getLoginUrl($params = array('scope' => "publish_stream"));
		    $redirectHtml = '
		    	<html><head>
			    <script type="text/javascript">
			      window.top.location.href = "' . $url . '";
			    </script>
			    <noscript>
			      <meta http-equiv="refresh" content="0;url=' . $url . '" />
			      <meta http-equiv="window-target" content="_top" />
			    </noscript>
			  </head></html>';
			die($redirectHtml);
		}

	
	}

	
	/**
	* Destrutor do objeto
	*/
	function __destruct(){
		$this->fechar();
		$this->amigo = null;
		$this->usuario = null;
		$this->facebook = null;
	}
	
	
	/**
	* Metodo responsavel por criar a conexao com o banco de dados
	*/
	private function conectar(){
		try{
			$this->conexao = new SQLite3($this->dbPath);
		} catch (Exception $e){
			die('Erro no banco de dados!');
		}
	}
	
	
	/**
	* Metodo responsavel por fechar a conexao com banco de dados
	*/
	private function fechar(){
		$this->conexao->close();
		$this->conexao = null;
	}

	
	/**
	* Metodo responsavel por verificar se o usuario ja recebeu um amigo
	* do dia. Caso o usuario ja tenha acessado o aplicativo, retorna o
	* amigo selecionado anteriormente.
	*/
	private function verificarAmigo(){
		$sql = "SELECT id_amigo FROM logs WHERE id_usuario = '{$this->usuario}' AND data = date('now') LIMIT 1";
		$resultado = $this->conexao->querySingle($sql);
		if (!empty($resultado)){
			$amigo = $this->facebook->api('/' . $resultado);
			return $amigo;
		} else {
			return '';
		}
	}


	
	/**
	* Metodo responsavel por obter um amigo aleatorio do usuario
	*/
	public function obterAmigo(){
		
		/**
		* Verifica se o usuario ja tem um amigo do dia
		*/
		$this->amigo = $this->verificarAmigo();

		/**
		* Se o usuario nao tiver recebido um amigo do dia, obtem os amigos do usuario, 
		* sorteia um amigo aleatorio, grava os dados do amigo no banco e publica uma 
		* mensagem no mural do usuario.
		*/
		if(empty($this->amigo)){
			$amigos = $this->facebook->api('/me/friends');
			$indice = array_rand($amigos['data']);
			$this->amigo  = $amigos['data'][$indice];
			$this->inserirAmigo();
			$this->publicarAmigo();
		}
		
		return $this->amigo;
			
	}

	
	/**
	* Metodo responsavel por inserir os dados do amigo no banco de dados
	*/
	private function inserirAmigo(){

		$ip = $_SERVER['REMOTE_ADDR'];
		
		$userAgent = $_SERVER['HTTP_USER_AGENT'];
		
		$sql = 	"INSERT INTO logs (`id_usuario`, 
					   `id_amigo`, 
					   `data`, 
				           `ip`,
					   `user_agent`)
					   VALUES (
					   '{$this->usuario}',
					   '{$this->amigo['id']}',
					   date('now'),
					   '{$ip}',
					   '{$userAgent}')";
					   
		$this->conexao->exec($sql);

	}


	/**
	* Metodo responsavel por publicar o amigo do dia no mural do usuario
	*/
	private function publicarAmigo(){
	
		$mensagem  = "Meu amigo do dia é {$this->amigo['name']}. ";
		//$mensagem .= "Descubra também seu amigo do dia! Acesse {$this->getAppUrl()}";

		try {
        	$params = array(
	            'message'       =>  $mensagem,
	            'name'          =>  "Daily Friend",
	            //'caption'       =>  "My Caption",
	            'description'   =>  "Descubra seu amigo do dia também!",
	            'link'          =>  $this->getAppUrl(),
	            'picture'       =>  "http://daily-friend.pagodabox.com/img/bg.png",
        	);

        	$post = $this->facebook->api("/me/feed","POST",$params);

        	return 'Mensagem publicada com sucesso!';

    	} catch (FacebookApiException $e) {
       		return 'Erro ao publicar mensagem no seu mural. Tente novamente mais tarde!';
    	}
	
	}

	
	/**
	* Metodo responsavel por enviar uma mensagem ao amigo
	*/
	public function enviarMensagem($mensagem){

		$post = array('message' => $mensagem);

		$url = '/' . $this->amigo['id'] . '/feed';

		if ($this->facebook->api($url, 'POST', $post)){
			return 'Mensagem enviada com sucesso!';
		} else {
			return 'Erro ao enviar mensagem! Tente novamente.';
		}		

	}
	
	
	/**
	* Metodo que retorna a URL do aplicativo 
	*/
	public function getAppUrl(){
		return $this->appUrl;
	}
	

}

?>

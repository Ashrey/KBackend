<?php
use \KBackend\Libs\AuthACL;
$HAANGA_VERSION  = '1.0.4';
/* Generated from /home/beto/www/backend/backend/app/views/default.phtml */
function haanga_6bba87bc970b59dc29cba79c827adb2fc22c06ab($vars155830aeba8a52, $return=FALSE, $blocks=array())
{
    extract($vars155830aeba8a52);
    if ($return == TRUE) {
        ob_start();
    }
    echo '<!DOCTYPE html>
<html lang="es">
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta charset="utf-8"> 
        <title>'.Config::get('backend.app.name').'</title>
        ';
    $buffer1  = '
            <link href="/backend/css/style.css" rel="stylesheet" type="text/css" />
        ';
    echo (isset($blocks['css']) ? (strpos($blocks['css'], '{{block.1b3231655cebb7a1f783eddf27d254ca}}') === FALSE ? $blocks['css'] : str_replace('{{block.1b3231655cebb7a1f783eddf27d254ca}}', $buffer1, $blocks['css'])) : $buffer1).'
    </head>
    <body>
        ';
    $buffer1  = '
        ';
    $login  = KumbiaAuth::isLogin();
    $vars155830aeba8a52['login']  = $login;
    $buffer1 .= '
<div class="navbar">
	<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
      <i class="icon-cogs"></i>
    </button>
	<a class="navbar-brand">Administración</a>
	<div class="collapse navbar-collapse">
		<ul class="nav navbar-nav">
		';
    if (empty($login) === FALSE) {
        $buffer1 .= '
			'.Menu::render(1).'
			<li>'.Html::link('profile/logout', 'Salir  <i class="fa fa-sign-out"></i>').'</li>
		';
    } else {
        $buffer1 .= '
			<li>'.Html::link('profile', 'Entrar').'</li>
		';
    }
    $buffer1 .= '
		</ul>
	</div>
</div>

        <div class="container">
            <div id="contenido" class="content">
            ';
    $buffer2  = '
                <header>
                    <h1>';
    $buffer3  = '';
    $buffer2 .= (isset($blocks['title']) ? (strpos($blocks['title'], '{{block.1b3231655cebb7a1f783eddf27d254ca}}') === FALSE ? $blocks['title'] : str_replace('{{block.1b3231655cebb7a1f783eddf27d254ca}}', $buffer3, $blocks['title'])) : $buffer3).'</h1>
                </header>
            ';
    $buffer1 .= (isset($blocks['header']) ? (strpos($blocks['header'], '{{block.1b3231655cebb7a1f783eddf27d254ca}}') === FALSE ? $blocks['header'] : str_replace('{{block.1b3231655cebb7a1f783eddf27d254ca}}', $buffer2, $blocks['header'])) : $buffer2).'
            ';
    $buffer2  = '

            ';
    $buffer1 .= (isset($blocks['content']) ? (strpos($blocks['content'], '{{block.1b3231655cebb7a1f783eddf27d254ca}}') === FALSE ? $blocks['content'] : str_replace('{{block.1b3231655cebb7a1f783eddf27d254ca}}', $buffer2, $blocks['content'])) : $buffer2).'
            </div>
        </div>
        ';
    echo (isset($blocks['body']) ? (strpos($blocks['body'], '{{block.1b3231655cebb7a1f783eddf27d254ca}}') === FALSE ? $blocks['body'] : str_replace('{{block.1b3231655cebb7a1f783eddf27d254ca}}', $buffer1, $blocks['body'])) : $buffer1).'
        <script type="text/javascript" src="/backend/javascript/zepto.kumbiaphp.js"></script>
        <script type="text/javascript" src="/backend/javascript/bootstrap.js"></script>
    </body>
</html>
';
    if ($return == TRUE) {
        return ob_get_clean();
    }
}
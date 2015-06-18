<?php
use \KBackend\Libs\AuthACL;
$HAANGA_VERSION  = '1.0.4';
/* Generated from /home/beto/www/kuword/default/app/views/default.phtml */
function haanga_0ed92697da43cf49b0543fa803a704afa13548f6($vars155830bfad6021, $return=FALSE, $blocks=array())
{
    extract($vars155830bfad6021);
    if ($return == TRUE) {
        ob_start();
    }
    echo '<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="/kuword/css/admin.css" rel="stylesheet" type="text/css" />
<link href="/kuword/css/basic.css" rel="stylesheet" type="text/css" />
<link href="/kuword/css/'.$style.'.css" rel="stylesheet" type="text/css" />
';
    if (empty($canonical) === FALSE) {
        echo '
	<link rel="canonical" href="'.htmlspecialchars($canonical).'" />
';
    }
    echo '
<title>'.htmlspecialchars($title).'</title>
</head>
<body>
    ';
    $buffer1  = '
	<header class="row" id="top">
		<h1 id="logo" class="col-6">'.htmlspecialchars($pagename).'</h1>
		<h2 id="slogan" class="col-6">'.htmlspecialchars($pagedesc).'</h2>
	</header>
    ';
    echo (isset($blocks['header']) ? (strpos($blocks['header'], '{{block.1b3231655cebb7a1f783eddf27d254ca}}') === FALSE ? $blocks['header'] : str_replace('{{block.1b3231655cebb7a1f783eddf27d254ca}}', $buffer1, $blocks['header'])) : $buffer1).'

    ';
    $buffer1  = '
	   <div id="menu">';
    $buffer2  = '
<ul id="menu">
';
    foreach ($menu as  $i) {
        $buffer2 .= '
	<li>'.Html::link($i->href, $i->showed_text).'</li>
';
    }
    $buffer2 .= '
</ul>
';
    $buffer1 .= (isset($blocks['menu']) ? (strpos($blocks['menu'], '{{block.1b3231655cebb7a1f783eddf27d254ca}}') === FALSE ? $blocks['menu'] : str_replace('{{block.1b3231655cebb7a1f783eddf27d254ca}}', $buffer2, $blocks['menu'])) : $buffer2).'
</div>
    ';
    echo (isset($blocks['menu']) ? (strpos($blocks['menu'], '{{block.1b3231655cebb7a1f783eddf27d254ca}}') === FALSE ? $blocks['menu'] : str_replace('{{block.1b3231655cebb7a1f783eddf27d254ca}}', $buffer1, $blocks['menu'])) : $buffer1).'

	<div class="container">
		';
    $buffer1  = '<h1>'.htmlspecialchars($ptitle).'</h1>';
    echo (isset($blocks['titlezone']) ? (strpos($blocks['titlezone'], '{{block.1b3231655cebb7a1f783eddf27d254ca}}') === FALSE ? $blocks['titlezone'] : str_replace('{{block.1b3231655cebb7a1f783eddf27d254ca}}', $buffer1, $blocks['titlezone'])) : $buffer1).'
        <div class="row">
		    <div class="col-12">';
    $buffer1  = '';
    echo (isset($blocks['content']) ? (strpos($blocks['content'], '{{block.1b3231655cebb7a1f783eddf27d254ca}}') === FALSE ? $blocks['content'] : str_replace('{{block.1b3231655cebb7a1f783eddf27d254ca}}', $buffer1, $blocks['content'])) : $buffer1).'</div>
        </div>
	</div>

    <footer class="text-center"><p>'.htmlspecialchars($footer).'<br />
    Powered By <a href="https://github.com/Ashrey/Kuword/">KuWord</a></p>
    </footer>
    ';
    $buffer1  = '';
    echo (isset($blocks['jsblock']) ? (strpos($blocks['jsblock'], '{{block.1b3231655cebb7a1f783eddf27d254ca}}') === FALSE ? $blocks['jsblock'] : str_replace('{{block.1b3231655cebb7a1f783eddf27d254ca}}', $buffer1, $blocks['jsblock'])) : $buffer1).'
</body>
</html>
';
    if ($return == TRUE) {
        return ob_get_clean();
    }
}
<?php
use \KBackend\Libs\AuthACL;
$HAANGA_VERSION  = '1.0.4';
/* Generated from /home/beto/www/kuword/default/app/views/index/index.phtml */
function haanga_d92ea561c551ffccf05586a24576c72757a7e8e2($vars155830ac0d0b13, $return=FALSE, $blocks=array())
{
    extract($vars155830ac0d0b13);
    if ($return == TRUE) {
        ob_start();
    }
    $buffer1  = '
<div id="list" class="row">
	';
    $total  = $pag->nextPage();
    $vars155830ac0d0b13['total']  = $total;
    $buffer1 .= '
';
    if ($total > 1) {
        $buffer1 .= '
';
        $next  = $pag->nextPage();
        $vars155830ac0d0b13['next']  = $next;
        $buffer1 .= '
';
        $prev  = $pag->prevPage();
        $vars155830ac0d0b13['prev']  = $prev;
        $buffer1 .= '

<div class="text-center">
    <ul class="pagination">
        ';
        if (!is_null($pag->prevPage())) {
            $buffer1 .= '
            
            ';
            $buffer2  = ''.str_replace('-_page_-', $prev, $url);
            $href  = $buffer2;
            $buffer1 .= '
            <li class="prev">'.Html::link($href, 'Anterior', 'title="Ir a la pág. anterior"').'</li>
        ';
        } else {
            $buffer1 .= '
            <li class="prev  disabled"><a title="Ir a la pág. anterior" href="#">Anterior</a></li>
        ';
        }
        $buffer1 .= '

        ';
        if (!is_null($pag->nextPage())) {
            $buffer1 .= '
            ';
            $buffer2  = ''.str_replace('-_page_-', $next, $url);
            $href  = $buffer2;
            $buffer1 .= '
            <li class="prev">'.Html::link($href, 'Siguiente', 'title="Ir a la pág. siguiente" class="nextprev"').'</li>
        ';
        } else {
            $buffer1 .= '
            <li class="prev disabled"><a title="Ir a la pág. siguiente" href="#">Siguiente</a></li>
        ';
        }
        $buffer1 .= '
    </ul>
</div>
';
    }
    $buffer1 .= '
	';
    foreach ($pag as  $i) {
        $buffer1 .= '
	<section class="col-4">
        <div class="post">
		<header>
            ';
        $buffer2  = 'index/post/'.htmlspecialchars($i->strid);
        $uri  = $buffer2;
        $buffer1 .= '
            <h2 class="t-post">'.Html::link($uri, $i->title).'</h2>
            <div class="info">
    			<img src="'.BlogUtil::image($i->content).'" />
    			<div class="calendar">
    				<span class="c-day">'.htmlspecialchars(date('d', strtotime($i->modified_in))).'</span>
    				<span class="c-year">
    					<i class="fa fa-calendar-o"></i> '.htmlspecialchars(date('Y', strtotime($i->modified_in))).'
    				</span>
    				'.htmlspecialchars(date('M', strtotime($i->modified_in))).'
    			</div>
            </div>
		</header>
		'.BlogUtil::trucate($i->content, 150).'
        </div>
	</section>
	';
    }
    $buffer1 .= '
	
</div>
';
    $total  = $pag->nextPage();
    $vars155830ac0d0b13['total']  = $total;
    $buffer1 .= '
';
    if ($total > 1) {
        $buffer1 .= '
';
        $next  = $pag->nextPage();
        $vars155830ac0d0b13['next']  = $next;
        $buffer1 .= '
';
        $prev  = $pag->prevPage();
        $vars155830ac0d0b13['prev']  = $prev;
        $buffer1 .= '

<div class="text-center">
    <ul class="pagination">
        ';
        if (!is_null($pag->prevPage())) {
            $buffer1 .= '
            
            ';
            $buffer2  = ''.str_replace('-_page_-', $prev, $url);
            $href  = $buffer2;
            $buffer1 .= '
            <li class="prev">'.Html::link($href, 'Anterior', 'title="Ir a la pág. anterior"').'</li>
        ';
        } else {
            $buffer1 .= '
            <li class="prev  disabled"><a title="Ir a la pág. anterior" href="#">Anterior</a></li>
        ';
        }
        $buffer1 .= '

        ';
        if (!is_null($pag->nextPage())) {
            $buffer1 .= '
            ';
            $buffer2  = ''.str_replace('-_page_-', $next, $url);
            $href  = $buffer2;
            $buffer1 .= '
            <li class="prev">'.Html::link($href, 'Siguiente', 'title="Ir a la pág. siguiente" class="nextprev"').'</li>
        ';
        } else {
            $buffer1 .= '
            <li class="prev disabled"><a title="Ir a la pág. siguiente" href="#">Siguiente</a></li>
        ';
        }
        $buffer1 .= '
    </ul>
</div>
';
    }
    $buffer1 .= '
';
    $blocks['content']  = (isset($blocks['content']) ? (strpos($blocks['content'], '{{block.1b3231655cebb7a1f783eddf27d254ca}}') === FALSE ? $blocks['content'] : str_replace('{{block.1b3231655cebb7a1f783eddf27d254ca}}', $buffer1, $blocks['content'])) : $buffer1);
    echo Haanga::Load('default.phtml', $vars155830ac0d0b13, TRUE, $blocks);
    if ($return == TRUE) {
        return ob_get_clean();
    }
}
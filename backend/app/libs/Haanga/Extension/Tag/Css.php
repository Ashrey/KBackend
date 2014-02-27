<?php

class Haanga_Extension_Tag_Css
{
    public $is_block = FALSE;

    static function generator($cmp, $args, $redirected)
    {
        $src = end($args[0]);
        $media = isset($args[1]['string']) ? "media={$args[1]['string']}":'';
        $code = hcode();
        $css  = '<link href="' . PUBLIC_PATH . "css/{$src}.css\" rel=\"stylesheet\" type=\"text/css\" $media/>";
        $cmp->do_print($code,  Haanga_AST::str($css));
        return $code;
    }
}

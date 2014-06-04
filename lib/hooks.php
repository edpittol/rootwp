<?php

class Hooks
{

    public static function admin_scripts()
    {
        wp_enqueue_media();
        wp_enqueue_script( 'root-dashboard', THEME_URL . 'lib/js/dashboard.js', array(), '', true );
        wp_enqueue_style( 'root-dashboard', THEME_URL . 'lib/css/dashboard.css', array(), '', 'screen' );
    }

}

?>
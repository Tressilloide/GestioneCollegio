<?php
    function filtro_testo($text){
        return addslashes(filter_var(trim($text), FILTER_SANITIZE_FULL_SPECIAL_CHARS));
    }        
?>

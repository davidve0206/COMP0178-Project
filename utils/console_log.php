<?php
// from https://stackify.com/how-to-log-to-console-in-php/#:~:text=There%20are%20two%20main%20ways,PHP%20libraries
function console_log($output, $with_script_tags = true) 
{
    $js_code = 'console.log(' . json_encode($output, JSON_HEX_TAG) . ');';
    if ($with_script_tags) {
        $js_code = "<script> $js_code </script>";
    }
    echo $js_code;
}
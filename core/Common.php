<?php
/**
 * 公共函数
 */

if(!function_exists('debug')){
    /**
     * [debug 打印调试函数]
     */
    function debug()
    {
        echo '<pre style="display: block;
        padding: 8.5px;
        margin: 0 0 9px;
        font-size: 12.025px;
        line-height: 18px;
        background-color: #f5f5f5;
        border: 1px solid #ccc;
        border: 1px solid rgba(0, 0, 0, 0.15);
        -webkit-border-radius: 4px;
        -moz-border-radius: 4px;
        border-radius: 4px;
        white-space: pre;
        white-space: pre-wrap;
        word-break: break-all;
        word-wrap: break-word;"><b style="color:red;">Debug:</b><br>';
        $params = func_get_args();
        foreach ($params as $param) {
            is_string($param) ? $tmp_param = trim($param) : $tmp_param = $param;
            if (is_bool($param) || empty($tmp_param)) {
                var_dump($param);
                echo '<hr />';
            } elseif ($param == 'form_data') {
                $info = '';
                foreach ($_POST as $k => $v) {
                    if ($k == 'submit') {
                        continue;
                    }
                    echo '$'.$k." = Request::Post('".$k."');".'<br>';
                    $info .= "'".$k."' => ".'$'.$k.',<br>';
                }
                $info = '$info = array(<br>'.$info.');<br>';
                echo '<br>'.$info.'<br>';
            } else {
                print_r($param);
                echo '<hr />';
            }
        }
        $debug = debug_backtrace();
        $debug_str = "in {$debug[0]['file']} on line {$debug[0]['line']}<br>";
        echo '<span style="color:green;">'.$debug_str.'</span></pre>';
    }
}


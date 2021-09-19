<?php
    class Routes {
        public static  function getPageName() {
            return str_replace('.php','',array_reverse(explode('/', $_SERVER['PHP_SELF']))[0]);
        }

        public static function getPageReference() {
            return (isset($_SERVER['HTTP_REFERER']))?$_SERVER['HTTP_REFERER']:null;
        }
    }
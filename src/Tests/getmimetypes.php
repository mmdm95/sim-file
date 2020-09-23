<?php

define('APACHE_MIME_TYPES_URL','http://svn.apache.org/repos/asf/httpd/httpd/trunk/docs/conf/mime.types');

function generateUpToDateMimeArray($url){
    $s=array();
    foreach(@explode("\n",@file_get_contents($url))as $x)
        if(isset($x[0])&&$x[0]!=='#'&&preg_match_all('#([^\s]+)#',$x,$out)&&isset($out[1])&&($c=count($out[1]))>1)
            for($i=1;$i<$c;$i++)
                $s[]='&nbsp;&nbsp;&nbsp;\''.$out[1][$i].'\' => \''.$out[1][0].'\'';
    return @sort($s)?'$mime_types = array(<br />'.implode(',<br />', $s).'<br />);':false;
}

echo generateUpToDateMimeArray(APACHE_MIME_TYPES_URL);

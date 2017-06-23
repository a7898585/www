<?php

/*
 * HTTP GET Request
*/
function get($url, $param = null) {
    if($param != null) {
        $query = http_build_query($param);
        $url = $url . '?' . $query;
    }   
    $ch = curl_init();
    if(stripos($url, "https://") !== false){
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    }   

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );
    $content = curl_exec($ch);
    $status = curl_getinfo($ch);
    curl_close($ch);
    if(intval($status["http_code"]) == 200) {
        return $content;
    }else{
        echo $status["http_code"];
        return false;
    }   
}

/*
 * HTTP POST Request
*/
function post($url, $params) {
    $ch = curl_init();
    if(stripos($url, "https://") !== false) {
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    }

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
    $content = curl_exec($ch);
    $status = curl_getinfo($ch);
    ECHO $content;
    curl_close($ch);
    if(intval($status["http_code"]) == 200) {
        return $content;
    } else {
        echo $status["http_code"];
        return false;
    }
}

function http_build_query_multi($params, $boundary) {
    if (!$params) return '';

    uksort($params, 'strcmp');

    $MPboundary = '--'.$boundary;
    $endMPboundary = $MPboundary. '--';
    $multipartbody = '';

    foreach ($params as $parameter => $value) {

        if( in_array($parameter, array('pic', 'image')) ) {
            $content = file_get_contents( $value );
            $filename = 'upload.jpg';

            $multipartbody .= $MPboundary . "\r\n";
            $multipartbody .= 'Content-Disposition: form-data; name="' . $parameter . '"; filename="' . $filename . '"'. "\r\n";
            $multipartbody .= "Content-Type: image/unknown\r\n\r\n";
            $multipartbody .= $content. "\r\n";
        } else {
            $multipartbody .= $MPboundary . "\r\n";
            $multipartbody .= 'content-disposition: form-data; name="' . $parameter . "\"\r\n\r\n";
            $multipartbody .= $value."\r\n";
        }
    }

    $multipartbody .= $endMPboundary;
    return $multipartbody;
}



?>

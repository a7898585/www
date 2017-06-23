<?php
exit();
function whois_query($domain, $host){
    $domain = strtolower(trim($domain));
    $domain = preg_replace('/^http:\/\//i', '', $domain);
    $domain = preg_replace('/^www\./i', '', $domain);
    $domain = explode('/', $domain);
    $domain = trim($domain[0]);
    $output = '';
    if ($conn = fsockopen ($host, 43)){
        fputs($conn, $domain."\r\n");
        while(!feof($conn)){
            $output .= fgets($conn,128);
        }
        fclose($conn);
    }else{
        die('Error: Could not connect to ' . $host . '!');
    }
    return nl2br($output);
}
$domain= $_GET["domain"];
$host= $_GET["host"];
echo whois_query($domain, $host);
?>

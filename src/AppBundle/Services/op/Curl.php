<?php

namespace AppBundle\Services\op;

/**
 * opCurl provides methods to use CURL
 *
 * http://www.getnetgoing.com/HTTP-505.html
 * @author Olivier LEQUEUX
 */
class Curl {

    public static $w3cStatusCodeDefinition = "http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html";
    static $HTTPCodeMap = array(1 => array(
    'category_label' => 'Informational',
    100=>'Continue',
    101=>'Switching Protocols'),
    2 => array(
    'category_label' => 'Successful',
    200=>"OK",
    201=>"Created",
    202=>"Accepted",
    203=>"Non-Authoritative Information",
    204=>"No Content",
    205=>"Reset Content",
    206=>"Partial Content"),
    3 => array(
    'category_label' => 'Redirection',
    300=>"Multiple Choices",
    301=>"Moved Permanently",
    302=>"Found",
    303=>"See Other",
    304=>"Not Modified",
    305=>"Use Proxy",
    306=>"(Unused)",
    307=>"Temporary Redirect"),
    4 => array(
    'category_label' =>'Client Error ',
    400=>"Bad Request",
    401=>"Unauthorized",
    402=>"Payment Required",
    403=>"Forbidden",
    404=>"Not Found",
    405=>"Method Not Allowed",
    406=>"Not Acceptable",
    407=>"Proxy Authentication Required",
    408=>"Request Timeout",
    409=>"Conflict",
    410=>"Gone",
    411=>"Length Required",
    412=>"Precondition Failed",
    413=>"Request Entity Too Large",
    414=>"Request-URI Too Long",
    415=>"Unsupported Media Type",
    416=>"Requested Range Not Satisfiable",
    417=>"Expectation Failed"),
    5 => array(
    'category_label' => 'Server Error',
    500=>"Internal Server Error",
    501=>"Not Implemented",
    502=>"Bad Gateway",
    503=>"Service Unavailable",
    504=>"Gateway Timeout",
    505=>"HTTP Version Not Supported")
    );


    /**
     *  return HTTP code description
     *
     * @param <integer> $HTTPCode
     *
     * @return <string> HTTP code description
     * @author  Olivier LEQUEUX
     */
    public static function getHTTPCodeDescription($HTTPCode) {
        $category=null;

        // search category
        $category = substr(strval($HTTPCode), 0,1);

        if(is_null($category) || !isset(self::$HTTPCodeMap[$category][$HTTPCode])) return $HTTPCode;

        return self::$HTTPCodeMap[$category][$HTTPCode];
    }

    /**
     *  return HTTP request info
     *
     * @param <string> $url url to check
     * @param <array> $options optional associative array for options
     *      AVAILABLE OPTIONS
     *          timeout : set timeout limit, set to self::HTTP_RESPONSE_DEFAULT_TIMEOUT by default
     *          ssl_cert : cert.pem file path (ssl mode)
     *          ssl_key : key.pem file path (ssl mode)
     *          ssl_cert_passwd : certificate password (ssl mode)
     *
     * @return <mixed> asked value if 'info_output' is defined, otherwise full info in associative array format
     * @author Cyril L'orphelin & Olivier LEQUEUX
     */
    public static function getHTTPResponse($url, array $options = array()) {

        $url = trim($url);

        $ch = curl_init($url); // init curl session
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, true); // no cache, force new connection



        // timeout values
        if(isset($options['timeout'])) {
            curl_setopt($ch, CURLOPT_TIMEOUT,$options['timeout']);
        }
        if(isset($options['connect_timeout'])) {
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $options['connect_timeout']);
        }

        if(isset($options['redirection'])) {
        curl_setopt($ch,CURLOPT_FOLLOWLOCATION,true);
        }

        // header
        curl_setopt($ch, CURLOPT_HEADER, false); // no header in string returned
        curl_setopt($ch, CURLOPT_HTTPHEADER, (isset($options['header'])) ? $options['header'] : array());
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // get result from request as string (otherwise through browser)

        // optional SSL part
        if (preg_match('`^https://`i', $url)) {

            if(!isset($options['ssl_cert']) || !isset($options['ssl_key']) || !isset($options['ssl_certpasswd'])) {
                throw new exception ('Unable to retreive HTTP response. Please set ssl_cert, ssl_key,sll_cert_passwd options to check url with SSL mode ');
            }
            curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
            curl_setopt($ch, CURLOPT_SSL_CIPHER_LIST, 'TLSv1');
            curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSLCERT, $options['ssl_cert']);
            curl_setopt($ch, CURLOPT_SSLKEY, $options['ssl_key']);
            curl_setopt($ch, CURLOPT_SSLCERTPASSWD,$options['ssl_certpasswd']);
        }



        $content = curl_exec($ch);
        $response = curl_getinfo($ch);
        $response['last_curl_error'] = curl_error($ch);
        $response['http_code_description'] = self::getHTTPCodeDescription($response['http_code']);

        $isValidContentType = true;
        if(isset($options['check_content_type'])) {
            $isValidContentType = (strpos($response['content_type'], $options['check_content_type']) !==false);
            // simulate 'non acceptable' http code
            if($isValidContentType == false) {
                $response['http_code'] = '406';
                $response['last_curl_error'] .= 'Content-type in header value must contain at least "'.$options['check_content_type'].'" , header of data received is "'.$response['content_type'].'"';
                $response['http_code_description'] = self::getHTTPCodeDescription($response['http_code']);
            }
        }

        // return content
        if(isset($options['get_content']) && ($options['get_content'] === true) && $isValidContentType) {
        // do not return content if it's not valid
            $response['content'] = $content;
        }

        curl_close($ch); // close  cURL session

        return $response;

    }



}


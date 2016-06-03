<?
/**
 * Class CBRequest
 * Для работы с $_GET, $_POST, $_REQUIRE запросами
 */
class CBRequest
{
    const SC_GET     = 1;
    const SC_POST    = 2;
    const SC_REQUEST = 3;
    const SC_COOKIE  = 4;
    const SC_SESSION = 5;
    const SC_SERVER  = 6;
    const SC_FILES   = 7;

    private $_requestUri;

    private $_scriptFile;
    private $_scriptUrl;
    private $_hostInfo;
    private $_url;

    private static $_instance = null;

    public function gi() {
        if (is_null(self::$_instance)) {
            self::$_instance = new CBRequest();
        }

        return self::$_instance;
    }
    /**
     * Initializes the application component.
     * This method overrides the parent implementation by preprocessing
     * the user request data.
     */
    private function __construct()
    {
        $this->normalizeRequest();
    }

    /**
     * Normalizes the request data.
     * This method strips off slashes in request data if get_magic_quotes_gpc() returns true.
     */
    protected function normalizeRequest()
    {
        // normalize request
        if(get_magic_quotes_gpc()){
            if(isset($_GET)){
                $_GET = $this->stripSlashes($_GET);
            }
            if(isset($_POST)) {
                $_POST = $this->stripSlashes($_POST);
            }
            if(isset($_REQUEST)) {
                $_REQUEST = $this->stripSlashes($_REQUEST);
            }
            if(isset($_COOKIE)) {
                $_COOKIE = $this->stripSlashes($_COOKIE);
            }
            if(isset($_SESSION)) {
                $_SESSION = $this->stripSlashes($_SESSION);
            }
        }
    }

    /**
     * Strips slashes from input data.
     * This method is applied when magic quotes is enabled.
     * @param mixed input data to be processed
     * @return mixed processed data
     */
    public function stripSlashes(&$data)
    {
        return is_array($data)? array_map(array($this, 'stripSlashes'), $data): stripslashes($data);
    }

    private function getScope($name, $scope, $default = null)
    {
        switch ($scope) {
            case self::SC_GET: $rArray = $_GET;
                break;
            case self::SC_POST: $rArray = $_POST;
                break;
            case self::SC_REQUEST: $rArray = $_REQUEST;
                break;
            case self::SC_SESSION: $rArray = $_SESSION;
                break;
            case self::SC_COOKIE: $rArray = $_COOKIE;
                break;
            case self::SC_SERVER: $rArray = $_SERVER;
                break;
            case self::SC_FILES: $rArray = $_FILES;
                break;

            default: $rArray = null;
        }
        if (!is_null($rArray) && isset($rArray[$name])) {
            return $rArray[$name];
        } else {
            return $default;
        }
    }

    /**
     * @return mixed Returns the named GET or POST parameter value.
     */
    public function getParam($name, $default = null)
    {
        return $this->getScope($name, self::SC_REQUEST, $default);
    }

    /**
     * @return mixed Returns the named GET parameter value.
     */
    public function getGET($name, $default = null)
    {
        return $this->getScope($name, self::SC_GET, $default);
    }

    /**
     * @return mixed Returns the named POST parameter value.
     */
    public function getPost($name, $default = null)
    {
        return $this->getScope($name, self::SC_POST, $default);
    }

    /**
     * @return mixed Returns the named SESSION parameter value.
     */
    public function getSession($name, $default = null)
    {
        return $this->getScope($name, self::SC_SESSION, $default);
    }

    /**
     * @return mixed Returns the named SESSION parameter value.
     */
    public function setSession($name, $value)
    {
        $_SESSION[$name] = $value;
    }

    /**
     * @return mixed Returns the named COOKIE parameter value.
     */
    public function getCookie($name, $default = null)
    {
        return $this->getScope($name, self::SC_COOKIE, $default);
    }

    /**
     * @return mixed Returns the named SERVER parameter value.
     */
    public function getServer($name, $default = null)
    {
        return $this->getScope($name, self::SC_SERVER, $default);
    }

    /**
     * @return mixed Returns the named FILES parameter value.
     */
    public function getFiles($name, $default = null)
    {
        return $this->getScope($name, self::SC_FILES, $default);
    }

    public function getRequestUri()
    {
        if($this->_requestUri === null){
            if(isset($_SERVER['HTTP_X_REWRITE_URL'])) { // IIS
                $this->_requestUri = $_SERVER['HTTP_X_REWRITE_URL'];
            } elseif (isset($_SERVER['REDIRECT_URL'])) {
                $this->_requestUri = $_SERVER['REDIRECT_URL'];
                if (isset($_SERVER['REDIRECT_QUERY_STRING'])) {
                    $this->_requestUri .= '?'. $_SERVER['REDIRECT_QUERY_STRING'];
                }
            } elseif(isset($_SERVER['REQUEST_URI'])){
                $this->_requestUri = $_SERVER['REQUEST_URI'];

                if(strpos($this->_requestUri, $_SERVER['HTTP_HOST']) !== false) {
                    $this->_requestUri = preg_replace('/^\w+:\/\/[^\/]+/', '', $this->_requestUri);
                }
                $this->_requestUri = substr($this->_requestUri, 0, strpos($this->_requestUri, '?'));

            } else {
                throw new sfErrorException('Request is unable to determine the request URI');
            }
        }

        return $this->_requestUri;
    }

    /**
     * Returns the relative URL of the entry script.
     * The implementation of this method referenced Zend_Controller_Request_Http in Zend Framework.
     * @return string the relative URL of the entry script.
     */
    public function getScriptUrl()
    {
        if($this->_scriptUrl === null)
        {
            $scriptName=basename($_SERVER['SCRIPT_FILENAME']);
            if(basename($_SERVER['SCRIPT_NAME'])===$scriptName)
                $this->_scriptUrl=$_SERVER['SCRIPT_NAME'];
            else if(basename($_SERVER['PHP_SELF'])===$scriptName)
                $this->_scriptUrl=$_SERVER['PHP_SELF'];
            else if(isset($_SERVER['ORIG_SCRIPT_NAME']) && basename($_SERVER['ORIG_SCRIPT_NAME'])===$scriptName)
                $this->_scriptUrl=$_SERVER['ORIG_SCRIPT_NAME'];
            else if(($pos=strpos($_SERVER['PHP_SELF'],'/'.$scriptName))!==false)
                $this->_scriptUrl=substr($_SERVER['SCRIPT_NAME'],0,$pos).'/'.$scriptName;
            else if(isset($_SERVER['DOCUMENT_ROOT']) && strpos($_SERVER['SCRIPT_FILENAME'],$_SERVER['DOCUMENT_ROOT'])===0)
                $this->_scriptUrl=str_replace('\\','/',str_replace($_SERVER['DOCUMENT_ROOT'],'',$_SERVER['SCRIPT_FILENAME']));

        }

        return $this->_scriptUrl;
    }

    /**
     * @return string part of the request URL that is after the question mark
     */
    public function getQueryString()
    {
        return isset($_SERVER['QUERY_STRING'])? $_SERVER['QUERY_STRING']: '';
    }

    /**
     * @return boolean if the request is sent via secure channel (https)
     */
    public function getIsSecureConnection()
    {
        return isset($_SERVER['HTTPS']) && !strcasecmp($_SERVER['HTTPS'], 'on');
    }

    /**
     * @return string request type, such as GET, POST, HEAD, PUT, DELETE.
     */
    public function getRequestType()
    {
        return strtoupper(isset($_SERVER['REQUEST_METHOD'])? $_SERVER['REQUEST_METHOD']: 'GET');
    }

    /**
     * @return boolean whether this is POST request.
     */
    public function getIsPostRequest()
    {
        return !strcasecmp($_SERVER['REQUEST_METHOD'], 'POST');
    }

    /**
     * @return boolean whether this is an AJAX (XMLHttpRequest) request.
     */
    public function getIsAjaxRequest()
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH'])? $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest': false;
    }

    /**
     * @return string server name
     */
    public function getServerName()
    {
        return $_SERVER['SERVER_NAME'];
    }

    /**
     * @return integer server port number
     */
    public function getServerPort()
    {
        return $_SERVER['SERVER_PORT'];
    }

    /**
     * @return string URL referrer, null if not present
     */
    public function getUrlReferrer()
    {
        return isset($_SERVER['HTTP_REFERER'])? $_SERVER['HTTP_REFERER']: null;
    }

    /**
     * @return string user agent
     */
    public function getUserAgent()
    {
        return isset($_SERVER['HTTP_USER_AGENT'])? $_SERVER['HTTP_USER_AGENT']: null;
    }

    /**
     * @return string user IP address
     */
    public function getUserIPAddress()
    {
        $ip = null;
        if (isset($_SERVER['HTTP_CLIENT_IP']) && $_SERVER['HTTP_CLIENT_IP']) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        }elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR']) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            if (!empty($_SERVER['HTTP_X_REAL_IP'])) {
                if (preg_match('/^(.+),\s*?'. preg_quote($_SERVER['HTTP_X_REAL_IP']) .'$/i', $ip, $m)) {
                    $ips = explode(',', trim($m[1]));
                    $ips = array_map('trim', $ips);
                    $ip = array_pop($ips);
                }
            }
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        return ($ip)? $ip: '127.0.0.1';
    }

    /**
     * @return string user IP address All
     */
    public function getUserIPAddressAll()
    {
        $ip = null;
        if (isset($_SERVER['HTTP_CLIENT_IP']) && $_SERVER['HTTP_CLIENT_IP']) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        }elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR']) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            if (!empty($_SERVER['HTTP_X_REAL_IP'])) {
                $_ip = explode(',', trim($ip));
                $_ip = array_map('trim', $_ip);
                $ip = null;
                foreach ($_ip as $val) {
                    if ($val != $_SERVER['HTTP_X_REAL_IP']) {
                        $ip[] = $val;
                    }
                }

                if ($ip) {
                    $ip = implode(', ', $ip);
                }
            }
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        return ($ip)? $ip: '127.0.0.1';
    }

    /**
     * @return string user host name, null if cannot be determined
     */
    public function getUserHost()
    {
        return isset($_SERVER['REMOTE_HOST'])? $_SERVER['REMOTE_HOST']: null;
    }


    /**
     * Returns information about the capabilities of user browser.
     * @param string the user agent to be analyzed. Defaults to null, meaning using the
     * current User-Agent HTTP header information.
     * @return array user browser capabilities.
     * @see http://www.php.net/manual/en/function.get-browser.php
     */
    public function getBrowser($userAgent = null)
    {
        return get_browser($userAgent, true);
    }

    /**
     * @return string user browser accept types
     */
    public function getAcceptTypes()
    {
        return isset($_SERVER['HTTP_ACCEPT'])? $_SERVER['HTTP_ACCEPT']: null;
    }

}

<?php  


abstract class API {
    /**
     * Property: method
     * The HTTP method this request was made in, either GET, POST, PUT or DELETE
     */
    protected $method = '';
    /**
     * Property: endpoint
     * The Model requested in the URI. eg: /files
     */
    protected $endpoint = '';
    /**
     * Property: verb
     * An optional additional descriptor about the endpoint, used for things that can
     * not be handled by the basic methods. eg: /files/process
     */
    protected $verb = '';
    /**
     * Property: args
     * Any additional URI components after the endpoint and verb have been removed, in our
     * case, an integer ID for the resource. eg: /<endpoint>/<verb>/<arg0>/<arg1>
     * or /<endpoint>/<arg0>
     */
    protected $args = Array();
    /**
     * Property: file
     * Stores the input of the PUT request
     */
    protected $file = Null;

    /**
     * Constructor: __construct
     * Allow for CORS, assemble and pre-process the data
     */
    public function __construct($request) {
    	header("Access-Control-Allow-Origin: *");
    	header("Access-Control-Allow-Methods: *");
    	header("Content-Type: application/json");

    	$this->args = explode('/', rtrim($request, '/'));
    	$this->endpoint = array_shift($this->args);
    	if (array_key_exists(0, $this->args) && !is_numeric($this->args[0])) {
    		$this->verb = array_shift($this->args);
    	}

    	$this->method = $_SERVER['REQUEST_METHOD'];

    	if ($this->method == 'POST' && array_key_exists('HTTP_X_HTTP_METHOD', $_SERVER)) {
    		if ($_SERVER['HTTP_X_HTTP_METHOD'] == 'DELETE') {
    			$this->method = 'DELETE';
    		} else if ($_SERVER['HTTP_X_HTTP_METHOD'] == 'PUT') {
    			$this->method = 'PUT';
    		} else {
    			throw new Exception("Unexpected Header");
    		}
    	}

    	switch($this->method) {
    		case 'POST':
    		$this->request = $this->_cleanInputs($_POST);
    		break;
    		case 'GET':
    		$this->request = $this->_cleanInputs($_GET);
    		break;
            case 'DELETE':
    		case 'PUT':
    		$this->request = $this->_cleanInputs($_GET);
    		$this->file = file_get_contents("php://input");
            $this->request = array_merge( $this->request, $this->parse_raw_http_request( $this->file ) );
            break;
            default:
            $this->_response('Invalid Method', 405);
            break;
        }
    }

    public function processAPI() {
    	// if ( (int) method_exists($this, $this->endpoint) > 0 ) {
    	if ( array_key_exists( $this->endpoint, Model::$db ) ) {
    		return $this->_response($this->GPPD( $this->endpoint, $this->args, $this->request ) );
    	}
    	return $this->_response("No Endpoint: $this->endpoint", 404);
    }

    private function _response($data, $status = 200) {
    	header("HTTP/1.1 " . $status . " " . $this->_requestStatus($status));
        // return json_encode($data);
        Model::$mysqli->close();
    	return $data;
    }

    private function _cleanInputs($data) {
    	$clean_input = Array();
    	if (is_array($data)) {
    		foreach ($data as $k => $v) {
    			$clean_input[$k] = $this->_cleanInputs($v);
    		}
    	} else {
    		$clean_input = trim(strip_tags($data));
    	}
    	return $clean_input;
    }

    private function _requestStatus($code) {
    	$status = array(  
    		200 => 'OK',
    		404 => 'Not Found',   
    		405 => 'Method Not Allowed',
    		500 => 'Internal Server Error',
    		); 
    	return ($status[$code])?$status[$code]:$status[500]; 
    }

    private function parse_raw_http_request( $input ) { 
        $a_data = array();
        preg_match('/boundary=(.*)$/', $_SERVER['CONTENT_TYPE'], $matches);
        $boundary = $matches[1];
        $a_blocks = preg_split("/-+$boundary/", $input);
        array_pop($a_blocks);
        foreach ($a_blocks as $id => $block) {
            if (empty($block)) continue;
            if (strpos($block, 'application/octet-stream') !== FALSE){
                preg_match("/name=\"([^\"]*)\".*stream[\n|\r]+([^\n\r].*)?$/s", $block, $matches);
            } else {
                preg_match('/name=\"([^\"]*)\"[\n|\r]+([^\n\r].*)?\r$/s', $block, $matches);
            }
            $a_data[$matches[1]] = $matches[2];
        }  
        return $a_data;      
    }

}

?>
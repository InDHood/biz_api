<?php  

require_once 'api.php';

class MyAPI extends API {

	// protected $User;
	protected $Request;

	public function __construct($request, $origin) {
		parent::__construct($request);

		$this->Request = $this->request;

		if (!array_key_exists('apiKey', $this->request)) {
			throw new Exception('No API Key provided');
		} else if ( !$this->verifyKey( $this->request['apiKey'], $origin ) ) {
			throw new Exception('Invalid API Key');
		} 

		// Token to be added later
		// if ( array_key_exists('token', $this->request ) && !$User->get('token', $this->request['token']) ) {
			// throw new Exception('Invalid User Token');
		// }

	}

    /**
    Endpoint
    */


    protected function GPPD( $tbl, $val, $data ) {

    	if ( $this->method == 'GET' ) {

    		$cols = Model::$db[$tbl]['cols'];
    		if (!empty($val[0])) $where = "id={$val[0]}"; else $where = '';
    		$limit = 50;
    		$order = 'id ASC';
    		return json_encode( Model::query( $tbl, $where, $order, $limit, $cols ) );

    	} else if ( $this->method == 'POST' ){

            if( $tbl == 'login' ) {

                return $this->login( $data );

            } else {

              $cols = Model::$db[$tbl]['fields'];
              foreach ($data as $k => $v) {
                 if ( in_array($k, $cols) ) $d[$k] = $v;
             }
             return Model::insert( $tbl, $d );
         }

    	} else if ( $this->method == 'PUT' ){ 

    		$cols = Model::$db[$tbl]['fields'];
    		if (!empty($val[0])) $where = "id={$val[0]}";
    		foreach ($data as $k => $v) {
    			if ( in_array($k, $cols) ) $d[$k] = $v;
    		}
    		return Model::update( $tbl, $d, $where );

    	} else if ($this->method == 'DELETE'){ 

    		if (!empty($val[0])) $d = array( "id"=>$val[0]);
    		return Model::delete( $tbl, $d );
    	}

    }


    protected function login( $data ) {
        
        $tbl    = 'users';
        $where  = " user = '{$data['user']}' && pass = '{$data['pass']}' ";
        $order  = '';
        $limit  = 1;
        $cols   = 'id';
        return json_encode( Model::query( $tbl, $where, $order, $limit, $cols ) );

    }


    /**
    API Key
    **/
    
    protected function verifyKey( $key ) {

    	// Check if Key Exist in Server, If Key Exist, Get User-Agent ID
    	$where = " api_key = '{$key}' ";

    	$q = Model::query( 'ar', $where, '', 1 );
    	if( !empty( $q['id'] ) ) {
    	// Log user-agent
    		$d['user_agent_id'] = $q['id'];
    		Model::insert( 'arl', $d );
    		return 1;
    	} else { 
    		return 0;
    	}

    }


}



?>
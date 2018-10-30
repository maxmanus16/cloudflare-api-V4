<?php
/**
 * User: Fatih Soysal & Luke Hardiman
 * Date: 10/30/2018
 * Cloudflare API.V4
 *
 * @author Fatih Soysal
 * @copyright Copyright (c) 2018, Fatih Soysal & Luke Hardiman
 * @link https://github.com/maxmanus16 | https://fatihsoysal.com
 */
class cloudflare_api
{
    
    
    //Timeout for the API requests in seconds
    const TIMEOUT = 5;
    
    private $URL = 'https://api.cloudflare.com/client/v4/';
    //Store Cloudflare email & auth
    private $auth_email;
    private $auth_key;
     
    //checking for valid dns type
    private static $VALID_DNS_TYPES = array('A', 'AAAA', 'CNAME', 'TXT', 'SRV', 'LOC', 'MX', 'NS', 'SPF');

    

    public function __construct()
    {
        
        $num_args = func_num_args();
        if ($num_args == 2){
            $parameters = func_get_args();
            $this->auth_email = $parameters[0];
            $this->auth_key = $parameters[1];
        }else{
            //throw error
        }
        
    }
    /**
    * identifier for domain
    */
    public function identifier($domain){
        $result = $this->get_zone($domain);
        if (isset($result->result) && count($result->result) == 1)
           return $result->result[0]->id;

       return false;
    }
     /**
    * analytics
    * @identifier
    * https://api.cloudflare.com/#zone-analytics-dashboard
    */
    public function analytics($identifier, $since = -10080, $until = 0 , $continuous = true){
        $continuous = ($continuous) ? "true" : "false";//need to pass as string
        $data = [
            'since'         => $since,
            'until'         => $until,
            'continuous'    => $continuous
        ];
        return $this->get('zones/'.$identifier.'/analytics/dashboard', $data);
    }
    /**
    * purge_files
    * @identifier
    * @files | Array
    */
    public function purge_files($identifier, $files = []){
        $data = [
            'files' => $files
        ];
        return $this->delete('zones/'.$identifier.'/purge_cache', $data);
    }
    /**
    * purge_site
    */
    public function purge_site($identifier){
        $data = [
            'purge_everything' =>  true
        ];
        return $this->delete('zones/'.$identifier.'/purge_cache',$data);
    }



     public function dev_mode($identifier){
        $data = [
            'value' =>  "on"
        ];
        return $this->patch('zones/'.$identifier.'/settings/development_mode',$data);
    }




    /**
    * dns_records
    */
    public function dns_records($identifier){
        return $this->get('zones/'.$identifier.'/dns_records',[]);
    }

   
    /**
    * get_dns_record_id
    * will return DNS id if only 1 record is found
    */
    public function get_dns_record_id($identifier,$type = '' ,$domain = ''){
        $response = $this->get_dns_record($identifier,$type,$domain);
        if ($response && count($response->result) == 1){
            return  $response->result[0]->id;
        }
        return "failed to get dns id use get_dns_record";
    }
    /**
    * get_dns_record
    * https://api.cloudflare.com/#dns-records-for-a-zone-list-dns-records
    */
    public function get_dns_record($identifier,$type = '' ,$domain = ''){

         if (!in_array($type, self::$VALID_DNS_TYPES))
            return "incorrect dns type";

        $data = [
            'type'      =>  $type,
            'name'      =>  $domain,
            'per_page'  =>  20,
            'order'     => 'type',
            'match'     => 'all'
        ];
        return $this->get('zones/'.$identifier.'/dns_records',$data);
    }
    /**
    * delete_dns_record
    * https://api.cloudflare.com/#dns-records-for-a-zone-delete-dns-record
    * @dns_record_id : DNS record ID
    */
    public function delete_dns_record($identifier,$dns_record_id){
        
        return $this->delete('zones/'.$identifier.'/dns_records/'.$dns_record_id,[]);
    }
     /**
    * update_dns_record
    * https://api.cloudflare.com/#dns-records-for-a-zone-update-dns-record
    * @type : A, AAAA, CNAME, TXT, SRV, LOC, MX, NS, SPF
    */
    public function update_dns_record($identifier,$dns_record_id,$type,$name,$content,$ttl = 1){

         if (!in_array($type, self::$VALID_DNS_TYPES))
         return "incorrect dns type";

        if($type === 'SRV'){
			//$content = "$weight, $priority, $target, $service, $proto, $port"
			//Example: $content = "3, 5, examplewebpage.com, _web, _tcp, 8765"
			$contentData = explode(", ", $content);
			
			$data = [
				"type"	=>	$type,
				"data"	=>	array(
					"name"	=>	$name,
					"weight" => $contentData[0],
					"priority" => $contentData[1],
					"target" => $contentData[2],
					"service" => $contentData[3],
					"proto" => $contentData[4],
					"port" => $contentData[5],
					"ttl"  => $ttl
					)
		];
		}else{
			$data = [
				'type'      =>  $type,
				'name'      =>  $name,
				'content'   =>  $content,
				'ttl'       =>  $ttl
			];
		}
        return $this->put('zones/'.$identifier.'/dns_records/'.$dns_record_id,$data);
    }
    /**
    * create_dns_record
    * https://api.cloudflare.com/#dns-records-for-a-zone-create-dns-record
    * @type : A, AAAA, CNAME, TXT, SRV, LOC, MX, NS, SPF
    */
    public function create_dns_record($identifier,$type,$name,$content,$ttl = 1){

         if (!in_array($type, self::$VALID_DNS_TYPES))
         return "incorrect dns type";

        if($type === 'SRV'){
			//$content = "$weight, $priority, $target, $service, $proto, $port"
			//Example: $content = "3, 5, examplewebpage.com, _web, _tcp, 8765"
			$contentData = explode(", ", $content);
			
			$data = [
				"type"	=>	$type,
				"data"	=>	array(
					"name"	=>	$name,
					"weight" => $contentData[0],
					"priority" => $contentData[1],
					"target" => $contentData[2],
					"service" => $contentData[3],
					"proto" => $contentData[4],
					"port" => $contentData[5],
					"ttl"  => $ttl
					)
		];
		}else{
			$data = [
				'type'      =>  $type,
				'name'      =>  $name,
				'content'   =>  $content,
				'ttl'       =>  $ttl
			];
		}
        return $this->post('zones/'.$identifier.'/dns_records',$data);
    }
    /**
    * purge_site
    */
    public function get_zone($name){
        $data = [
            'name'      => $name,
            'status'    => 'active',
            'page'      => 1,
            'match'     => 'all'
        ];
        return $this->get('zones',$data);
    }
    /**
    * purge_site
    */
    public function get_zones(){

        $data = [
            'status'    => 'active',
            'page'      => 1,
            'match'     => 'all',
            'per_page' => '999'
        ];

        return $this->get('zones',$data);
    }

    /**
     * User Section
     */


    /**
     * get_user_details
     * https://api.cloudflare.com/#user-user-details
     */
    public function get_user_details(){
        return $this->get('user',[]);
    }
    /**
     * update_user_details
     * https://api.cloudflare.com/#user-user-details
     */
    public function update_user_details($first_name,$last_name,$telephone,$country,$zipcode){
        $data = [
            "first_name" => $first_name,
            "last_name"  => $last_name,
            "telephone"  => $telephone,
            "country"    => $country,
            "zipcode"    => $zipcode
        ];
        return $this->patch('user',$data);
    }

    //privates below
    /**
    * delete
    */
    private function delete($endpoint,$data){
        return $this->http_request($endpoint,$data,'delete');
    }
    /**
    * post
    */
    private function post($endpoint,$data){
        return $this->http_request($endpoint,$data,'post');
    }
    /**
    * get
    */
    private function get($endpoint,$data){
        return $this->http_request($endpoint,$data,'get');
    }
    /**
    * put
    */
    private function put($endpoint,$data){
        return $this->http_request($endpoint,$data,'put');
    }
    /**
     * patch
     */
    private function patch($endpoint,$data){
        return $this->http_request($endpoint,$data,'patch');
    }
    /**
    * Handle http request to cloudflare server
    */
    private function http_request($endpoint,$data, $method)
    {
        //setup url
        $url = $this->URL.$endpoint;
        
        //echo $url;exit;
        
        //headers set
        $headers = ["X-Auth-Email: {$this->auth_email}", "X-Auth-Key: {$this->auth_key}"];
        $headers[] = 'Content-type: application/json';
        
        //json encode data
        $json_data = json_encode($data);
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_VERBOSE, 0);
        curl_setopt($ch, CURLOPT_FORBID_REUSE, true);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        curl_setopt($ch, CURLOPT_TIMEOUT, self::TIMEOUT);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        
        if ($method === 'post')
            curl_setopt($ch, CURLOPT_POST, true);
        
        if ($method === 'put')
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        
        if ($method === 'delete')
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');

        if ($method === 'patch')
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
    
        //get request otherwise pass post data
        if (!isset($method) || $method == 'get')
            $url .= '?'.http_build_query($data);
        else
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
        //echo $url;

        //add headers
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_URL, $url);
        
        
        $http_response = curl_exec($ch);
        $error       = curl_error($ch);
        $http_code   = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
       

        if ($http_code != 200) {
            //hit error will add in error checking but for now will return back to user to handle
            return json_decode($http_response);
        } else {
            return json_decode($http_response);
        }
    }
}

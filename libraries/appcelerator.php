<?php
/**
 * CodeIgniter Appcelerator Library
 *
 * @category  Library
 * @package   CodeIgniter
 * @author    Ben Edmunds <http://benedmunds.com>
 * @author    Shealan Foreshaw <http://twitter.com/#!/shealan>
 * @company	  Swipe & Tap <http://twitter.com/#!/swipeandtap>	
 * @copyright 2012 Ben Edmunds
 * @license   MIT License http://www.opensource.org/licenses/mit-license.php
 * @version   Release: 1.0
 * @link      https://github.com/benedmunds/CodeIgniter-Appcelerator
 */

class Appcelerator
{
	protected $base_url    = 'api.cloud.appcelerator.com/v1/';
	protected $key         = '';
	protected $email       = '';
	protected $password    = '';
	protected $cookie      = '/tmp/appccookie';
	protected $_logged_in  = FALSE;
	protected $_errors     = '';

	function __construct($key=NULL, $email=NULL, $password=NULL)
	{
		$this->cookie .= time() . '.txt';

		if (isset($key))
		{
			$this->key = $key;
		}

		if (isset($email) && isset($password))
		{
			$this->email    = $email;
			$this->password = $password;
		}
	}

	function init($key, $email, $password)
	{
		$this->key      = $key;
		$this->email    = $email;
		$this->password = $password;
	}

	function send_request($url, $method, $data, $secure=TRUE)
	{
		$url = $this->_build_url($url, $secure) . ($method=='GET' ? '&'.http_build_query($data) : '');

		if ($this->_logged_in === FALSE)
		{
			$login = $this->_login($this->email, $this->password);
			
			if ($login == FALSE)
			{
				return json_decode($this->_errors);
			}
		}

		$ch = curl_init($url);

		curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookie);
		curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookie);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE); 
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

		switch ($method)
		{
			case 'GET':
		 		curl_setopt($ch, CURLOPT_HTTPGET, TRUE);
				break;
			case 'POST':
		 		curl_setopt($ch, CURLOPT_POST, TRUE);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
				break;
			case 'PUT':
		 		curl_setopt($ch, CURLOPT_PUT, TRUE);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
				break;
			case 'DELETE':
		 		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
				curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
				break;
		}
		 
		$output = curl_exec($ch);

		if ($output == FALSE)
		{
			return curl_error($ch);
		}

		return json_decode($output);
	}

	protected function _login($email, $password)
	{
		$login = array(
			'login'    => $email, 
			'password' => $password
		);

		$ch = curl_init($this->_build_url('users/login.json'));
		 
		curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookie);
		curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookie);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE); 
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	 	curl_setopt($ch, CURLOPT_POST, TRUE);			 
		curl_setopt($ch, CURLOPT_POSTFIELDS, $login);
		 
		$login = curl_exec($ch);
		
		if ($login == FALSE)
		{
			$this->_errors = curl_error($ch);
			$this->_logged_in = FALSE;
			return FALSE;
		}

		$this->_logged_in = TRUE;
		return TRUE;
	}

	protected function _build_url($url, $secure=TRUE)
	{
		$final_url = '';
		$final_url  = ($secure === TRUE) ? 'https://' : 'http://';
		$final_url .= $this->base_url;
		$final_url .= $url . '?key=' . $this->key;

		return $final_url;
	}
}
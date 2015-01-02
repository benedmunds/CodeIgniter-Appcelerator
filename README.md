#Appcelerator ACS API library for CodeIgniter  
A CodeIgniter library that is a simple port of the Appcelerator ACS Javascript SDK to PHP  

##Install
Install the library using sparks

```bash
$ php tools/spark install appcelerator
```

##Usage
In your controller simply load the library, set your login info, and call the API
  
protected $app_key  = '123';
protected $email    = 'ben.edmunds@gmail.com';
protected $password = '12345678';

```php
function statuses() 
{
	$this->load->library('appcelerator');

	$this->appcelerator->init($this->app_key, $this->email, $this->password);

	$data = array(
		'where' => json_encode(array('user_id' => '4f9eb57a0020440def0056d3')),	
	);

	$output = $this->appcelerator->send_request('statuses/query.json', 'GET', $data);

	print_r($output);
	exit;
}

function create_status() 
{
	$this->load->library('appcelerator');

	$this->appcelerator->init($this->app_key, $this->email, $this->password);

	$data = array(
		'message' => 'api test message',	
	);

	$output = $this->appcelerator->send_request('statuses/create.json', 'POST', $data);
	var_dump($output);
	exit;
}
```

See [the Appcelerator documentation](http://cloud.appcelerator.com/docs/api/v1/statuses/info) for API details.

Library created by [Ben Edmunds](http://benedmunds.com) and [Shealan Foreshaw](http://twitter.com/#!/shealan) for [Swipe & Tap](http://twitter.com/#!/swipeandtap).

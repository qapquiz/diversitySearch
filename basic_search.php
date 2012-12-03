<?php
  $acctKey = 'bAJBaFvnuCrgiaAW9ZtxdG4AAW6RnA7xs/uwLde1zao=';
  $rootUri = 'https://api.datamarket.azure.com/Bing/Search';
  
  $contents = file_get_contents('Diversity_Search.html');
  
  if($_POST['query']){
	  $query = urldecode("'{$_POST['query']}'");
	  $serviceOp = $_POST['service_op'];
	  $requestUri = "$rootUri/$serviceOp?\$format=json&query=$query";
	  
	  $auth = base64_encode("$acctKey:$acctKey");
	  $data = array(
	    'http'=>array(
		  'request_fulluri'=>true,
		  'ignore_errors'=>true,
		  'header'=>"Authorization: Basic $auth"
		)
	  );
	  $context = stream_context_create($data);
	  
	  $response=file_get_contents($requestUri, 0, $context);
	  
	  $jsonObj = json_decode($response);
	  $resultStr = "";
	  
	  foreach($jsonObj->d->results as $value){
		switch($value->__metadata->type){
			case 'WebResult':
			  $resultStr .= "<a href=\"{$value->Url}\">{$value->Title}</a><p>{$value->Description}</p>"; 
			  break; 
			case 'ImageResult': 
			  $resultStr .= "<h4>{$value->Title} ({$value->Width}x{$value->Height}) " . "{$value->FileSize} bytes)</h4>" . "<a href=\"{$value->MediaUrl}\">" . "<img src=\"{$value->Thumbnail->MediaUrl}\"></a><br />"; 
			  break;	
		}
	  }
	  $contents = str_replace('{RESULTS}', $resultStr, $contents);
  }
  
  echo $contents
?>
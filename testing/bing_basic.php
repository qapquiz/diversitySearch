<?php
    $acctKey = "bAJBaFvnuCrgiaAW9ZtxdG4AAW6RnA7xs/uwLde1zao=";
    $rootUri = 'https://api.datamarket.azure.com/Bing/Search';
	
	//initialize
	$top = 100;
	$countIndex = 0;
	$relatedArray = array();
    
    //read the content of html file into string
    $contents = file_get_contents("bing_search.html");
    
    if($_POST['query'])
    {
        //process query
        //encode the query 
        $query = urlencode("'{$_POST['query']}'");
        
        //get the selected sevice operation
        $serviceOp = $_POST['service_op'];
        
        //construct the full url for the query
        $requestUri = "$rootUri/$serviceOp?\$format=json&Query=$query";
        
        //encode the credentials and create the stream context
        $auth = base64_encode("$acctKey:$acctKey");
        $data = array(
            'http' => array(
                'request_fulluri' => true,
                'ignore_errors' => true,
                'header' => "Authorization: Basic $auth")
            );
        $context = stream_context_create($data);
        
        //get some response from bing
        $response = file_get_contents($requestUri, 0, $context);
        
        //decode the response
        $jsonObj = json_decode($response);
        
        $resultStr = "";
        
        //parse each result according to its metadata type
        foreach($jsonObj->d->results as $value)
        {
            switch($value->__metadata->type)
            {
                case 'WebResult':
                    $resultStr .= "<a href=\"{$value->Url}\">{$value->Title}</a><p>{$value->Description}</p>";
                    break;
                case 'ImageResult':
                    $resultStr .=  "<h4>{$value->Title} ({$value->Width}x{$value->Height}) " . "{$value->FileSize} bytes)</h4>" . "<a href=\"{$value->MediaUrl}\">" . "<img src=\"{$value->Thumbnail->MediaUrl}\"></a><br />"; 
                    break;
				case 'RelatedSearchResult':
					$relatedArray[$countIndex] = "{$value->Title}";
					$countIndex = $countIndex + 1;
					$resultStr .= "<p>{$value->Title}</p>";
					break;
            }
        }
		$countIndex = 0;
		//this line get the array of related word
        
        //substitute the results placeholder
        $contents = str_replace('{RESULTS}', $resultStr, $contents);
        
    }
    
    echo $contents;
?>
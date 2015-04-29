<?php
/**
 * Total download size of any URL controller.
 *
 * This file will render views from views/Downloadcount/
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

App::uses('AppController', 'Controller');
App::import('Vendor', 'simple_html_dom');

/**
 * Static content controller
 *
 * Override this controller by placing a copy in controllers directory of an application
 *
 * @package       app.Controller
 * @link http://book.cakephp.org/2.0/en/controllers/pages-controller.html
 */
class DownloadcountController extends AppController {

/**
 * This controller does not use a model
 *
 * @var array
 */
	public $uses = array();

/**
 * Displays a view
 *
 * @return void
 * @throws NotFoundException When the view file could not be found
 *	or MissingViewException in debug mode.
 */
	public function urlDetails() {

		$fileSize=0;
		$totalNumResources = 0;
		$check_if_html='';
		if(isset($this->request->data) && !empty($this->request->data))
		{
			
			$url=$this->request->data['url'];
			$parse = parse_url($url);
			$fileSize=get_file_size($url);
			$check_if_html=check_if_html($url);

			
			if (!$check_if_html)
				{
					$fileSize=get_file_size($url);
				}
				else
				{

					$fileSize=0;
					
					$html = file_get_html($url);

					// find all images:
					foreach($html->find('img') as $element){
						   if (strpos($element->src, 'http') === 0 || strpos($element->src, 'www') === 0) {
							    
							    $source=$element->src;
							}
							else
								$source = $parse['host'].$element->src;


						   $size = get_file_size($source);
							
						   $fileSize = $fileSize + $size; 	
						   
						   $totalNumResources += 1;

						
					}

					// find all iframes:
					foreach($html->find('iframe') as $element){
						   if (strpos($element->src, 'http') === 0 || strpos($element->src, 'www') === 0) {
							    
							    $source=$element->src;
							}
							else
								$source = $parse['host'].$element->src;


						   $size = get_file_size($source);
							
						   $fileSize = $fileSize + $size; 	
						   
						   $totalNumResources += 1;

						
					}

					// Find all CSS:
					foreach($html->find('link') as $element)
					{

						if (strpos($element->href,'.css') !== false) {
						if (strpos($element->href, 'http') === 0 || strpos($element->src, 'www') === 0) {
							$source=$element->href;    
							}
							else
								$source = $parse['host'].$element->href;


						  $size = $size = get_file_size($source);
						   
						  $fileSize = $fileSize + $size; 
						   	   
						  $totalNumResources += 1;
						
						
						}
					     //only output the ones with 'css' inside...
					}


					//find all javascript:
					foreach($html->find('script') as $element)
					{

					//check to see if it is javascript file:
					if (strpos($element->src,'.js') !== false) {
						 if (strpos($element->src, 'http') === 0 || strpos($element->src, 'www') === 0) {
							    $source=$element->src;
							}
							else
								$source = $parse['host'].$element->src;
						 
						  $size = get_file_size($source);
						  
						  //echo " JS SIZE: $size.\n"; 

						 $fileSize = $fileSize + $size; 		
						  	   
						 $totalNumResources += 1;
						 
						
						}
					}


					
				}
			
			$fileSize=$fileSize/1024;
		}


		$this->request->data['check_if_html']=$check_if_html;
		$this->request->data['total_num_resources']=$totalNumResources;
		$this->request->data['size']=$fileSize;

	}
}


function get_file_size($url) {

 	$headers = @get_headers($url, 1);
    
    if (isset($headers['Content-Length'])) 
       return $headers['Content-Length'];
    
    //checks for lower case "L" in Content-length:
    if (isset($headers['Content-length'])) 
       return $headers['Content-length'];

    //the code below runs if no "Content-Length" header is found:
    $c = curl_init();
    curl_setopt_array($c, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => array('User-Agent: Mozilla/5.0 
        (Macintosh; U; Intel Mac OS X 10.5; en-US; rv:1.9.1.3) 
        Gecko/20090824 Firefox/3.5.3'),
        ));
    curl_exec($c);
    
    $size = curl_getinfo($c, CURLINFO_SIZE_DOWNLOAD);
    
    return $size;
        
    curl_close($c);

}


function check_if_html($url){


/*	$ch = curl_init('http://www.google.com');
	 curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
 if(curl_exec($ch) === false)
{
    echo 'Curl error: ' . curl_error($ch);
}
else
{
    echo 'Operation completed without any errors';
}
  # get the content type
  $content_type = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
  echo "=================".$content_type;
curl_close($ch);*/



     $ch = curl_init($url);

     curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
     curl_setopt($ch, CURLOPT_HEADER, TRUE);
     curl_setopt($ch, CURLOPT_NOBODY, TRUE);

     
		if(curl_exec($ch) === false)
		{
			$data = curl_exec($ch);
		    echo 'Curl error: ' . curl_error($ch);
		}
		else
		{
		    echo 'Operation completed without any errors';
		}
     $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE );

     curl_close($ch);
     
     if (strpos($contentType,'text/html') !== false)
	 	return TRUE; 	// this is HTML, yes!
	 else
	    return FALSE;
}

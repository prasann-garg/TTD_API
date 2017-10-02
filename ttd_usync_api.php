<?php

    $secreKey = "TTD_SECRET_KEY";
    
    $ttd_usync_error_log = "/mnt/logs/ttdUsyncError.log";
    $loggerString = date("Y-m-d H:i:s")." : Error : ";
    
    $requestData = array();
    $dataArr = array();
    $itemsArr = array();
    
    $ttl = isset($_GET['ttl']) ? ($_GET['ttl'] * 24 * 60) : 43200; // Default 30 days ttl value expected in days
    if(!empty($_GET['segmentId']))
    {
       $data = array();
       $data['Name'] = $_GET['segmentId'];
       $data['TimestampUtc'] = gmdate("Y-m-d\TH:i:s\Z");
       $data['TtlInMinutes'] = $ttl;
       $dataArr[] = $data;   
    }
    
    if(!empty($_GET['TDID']))
    {
        $item = array();
        $items['TDID'] = $_GET['TDID'];
        $items['Data'] = $dataArr;
        $itemsArr[] = $items;
    }
    
    $requestData['AdvertiserId'] = $_GET['AdvertiserId']; 
    $requestData['Items'] = $itemsArr;
    
    $data = json_encode($requestData);
     
    $hmac = hash_hmac('sha1', $data, $secreKey, true);
    $bodyHash = base64_encode($hmac);
    
    $url = 'http://use-data.adsrvr.org/data/advertiser';
    
    $ch = curl_init($url);
    
    curl_setopt($ch, CURLOPT_POST, 1);
    
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'TtdSignature: '.$bodyHash));
    
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    //$info = curl_getinfo($ch);
    
    //print_r($info);
    
    $result = curl_exec($ch);
    echo $result;
    
    if(!empty($result))
    {
        error_log($loggerString.$result . " Request Details: ".print_r($_GET, true)."\n", 3, $ttd_usync_error_log );
    }

?>
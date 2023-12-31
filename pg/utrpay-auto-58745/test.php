<?php

$token = "5xyNgvqTe1efmK4ZowwaHA==.19hemahk9di8uijk2iagk5tp9p";

$curl = curl_init();

curl_setopt_array($curl, array(
CURLOPT_URL => 'https://webapi.mobikwik.com/p/wallet/history/v2',
CURLOPT_RETURNTRANSFER => true,
CURLOPT_ENCODING => '',
CURLOPT_MAXREDIRS => 10,
CURLOPT_TIMEOUT => 120,
CURLOPT_FOLLOWLOCATION => true,
CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
CURLOPT_CUSTOMREQUEST => 'GET',
CURLOPT_HTTPHEADER => array(
'Authorization: '.$token,
'X-Mclient: 0',
'user-agent: Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/112.0.0.0 Mobile Safari/537.36'
),
));
        
$response = curl_exec($curl);
curl_close($curl);

$jsonArr = json_decode($response,true);
$jsonArrLen = sizeof($jsonArr['data']['historyData']);
$arrList = $jsonArr['data']['historyData'];

echo "<pre>";
print_r($jsonArr['data']['historyData']);


for ($x = 0; $x < $jsonArrLen; $x++) {
    echo $arrList[$x]['rrn'].'/';
    echo $arrList[$x]['date'].'/';
}

<?php

$channelAccessToken = '742Fl9coFh/IhuKPaFUhi5lPhlXA6nDg/jeLcHwS4S8PQ6dNx6m15sSXmmw4b6kBvj/fCI6uSB35oYoDHxh4cd625YnjK6eT2tSoO67MiUx3y/lNmxjP49cHjjEax0HJRw3tO5sUkSeC9comlSkcBAdB04t89/1O/w1cDnyilFU='; // Access Token ค่าที่เราสร้างขึ้น

$request = file_get_contents('php://input');   // Get request content

$request_json = json_decode($request, true);   // Decode JSON request

foreach ($request_json['events'] as $event)
{
	if ($event['type'] == 'message') 
	{
		if($event['message']['type'] == 'text')
		{
			$text = $event['message']['text'];
			$arr = explode(" ",$text); 
			$reply_message = $arr;
// 			$reply_message = mySQL_selectAll('http://bot.kantit.com/json_select_users.php?sid='.$text);	
		} else {
			$reply_message = 'ฉันได้รับ '.$event['message']['type'].' ของคุณแล้ว!';
		}
	} else {
		$reply_message = 'ฉันได้รับ Event '.$event['type'].' ของคุณแล้ว!';
	}
	
	if($reply_message == null || $reply_message == ""){ $reply_message =  'ขออภัยฉันไม่สามารถตอบกลับข้อความ "'. $text . '" ของคุณ!'; }
	// reply message
	$post_header = array('Content-Type: application/json', 'Authorization: Bearer ' . $channelAccessToken);
	$data = ['replyToken' => $event['replyToken'], 'messages' => [['type' => 'text', 'text' => $reply_message]]];
	$post_body = json_encode($data);
	$send_result = replyMessage('https://api.line.me/v2/bot/message/reply', $post_header, $post_body);
	//$send_result = send_reply_message('https://api.line.me/v2/bot/message/reply', $post_header, $post_body);
}

function replyMessage($url, $post_header, $post_body)
{
        $context = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => $post_header,
                'content' => $post_body,
            ],
        ]);
	
	$result = file_get_contents($url, false, $context);

	return $result;
}

function send_reply_message($url, $post_header, $post_body)
{
	$ch = curl_init($url);	
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $post_header);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $post_body);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	
	$result = curl_exec($ch);
	
	curl_close($ch);
	
	return $result;
}

function mySQL_selectAll($url)
{
	$result = file_get_contents($url);
	
	$result_json = json_decode($result, true); //var_dump($result_json);
	
	$data = "ผลลัพธ์:\r\n";
		
	foreach($result_json as $values) {
		$data .= $values["user_stuid"] . " " . $values["user_firstname"] ." ". $values["user_lastname"]."\r\n";
	}
	
	return $data;
}

?>

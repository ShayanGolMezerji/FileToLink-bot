<?php

// Made by @ShayanGolMezerji

error_reporting(1);
$token = "7303088829:AAFAcP6JBAxZCqNQ-2aVcGKAK721gJtb1xI"; 
$domain = "https://festoir.000webhostapp.com/FileTOLink/php.php"; 
define('API_KEY', $token); 

function bot($method, $datas = []) {
    $url = "https://api.telegram.org/bot" . API_KEY . "/" . $method;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $datas);
    $res = curl_exec($ch);
    return curl_error($ch) ? curl_error($ch) : json_decode($res);
}

function ismember($from_id, $channel) {
    global $token;
    $decode = json_decode(file_get_contents("https://api.telegram.org/bot$token/getChatMember?chat_id=$channel&user_id=$from_id"));
    $tch = $decode->result->status;
    return in_array($tch, ['member', 'administrator', 'creator']);
}

function isadmin($from_id, $group) {
    global $token;
    $decode = json_decode(file_get_contents("https://api.telegram.org/bot$token/getChatMember?chat_id=$group&user_id=$from_id"));
    $tch = $decode->result->status;
    return in_array($tch, ['administrator', 'creator']);
}

function startsWith($string, $startString) {
    return strpos($string, $startString) === 0;
}

function sendFile($chat_id, $file_id, $message_id, $domain, $token, $type) {
    $get = bot("getFile", ['file_id' => $file_id]);
    $patch = $get->result->file_path;
    $url = "https://api.telegram.org/file/bot$token/$patch";
    $name = ($type == 'document') ? str_replace("documents/", "", $patch) : rand(0, 1000) . ".$type";
    file_put_contents($name, file_get_contents($url));

    bot('send' . ucfirst($type), [
        'chat_id' => $chat_id,
        $type => "$domain/$name",
        'caption' => "Your file has been downloaded and sent",
        'reply_to_message_id' => $message_id,
    ]);
    unlink($name);
}

$update = json_decode(file_get_contents('php://input'));
$message = $update->message;
$chat_id = $message->chat->id;
$text = $message->text;
$from_id = $message->from->id;
$message_id = $message->message_id;

if ($text == "/start") {
    bot('sendMessage', [
        'chat_id' => $chat_id,
        'text' => "Hey there! Please send your file and I will give you a link. (Made by @ShayanGolmezerji)",
        'reply_to_message_id' => $message_id,
    ]);
} else if ($file = $message->photo[0] ?? $message->document ?? $message->audio ?? $message->video ?? null) {
    $file_id = is_array($file) ? $file[count($file) - 1]->file_id : $file->file_id;
    bot('sendmessage', [
        'chat_id' => $chat_id,
        'text' => "Your file access code : <code>$file_id</code>\n\nNote: Your access key will not be stored anywhere and only you have access to it",
        'reply_to_message_id' => $message_id,
        'parse_mode' => 'html',
    ]);
} else {
    $types = [
        "AgACA" => "photo",
        "BAACA" => "video",
        "CQACA" => "audio",
        "BQACA" => "document",
        "CgACA" => "animation",
    ];
    foreach ($types as $prefix => $type) {
        if (startsWith($text, $prefix)) {
            sendFile($chat_id, $text, $message_id, $domain, $token, $type);
            break;
        }
    }
}

?>

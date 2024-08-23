<?php

// Made by @ShayanGolMezerji

ob_start();
flush();

error_reporting(1);
$token = "Token"; //Token
$domain="https://example.com/filetolink.php"; //Domain & file
define('API_KEY',$token); 

function bot($method,$datas=[]){
$url = "https://api.telegram.org/bot".API_KEY."/".$method;
$ch = curl_init();
curl_setopt($ch,CURLOPT_URL,$url);
curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
curl_setopt($ch,CURLOPT_POSTFIELDS,$datas);
 $res = curl_exec($ch);
 if(curl_error($ch)){
var_dump(curl_error($ch));
}else{
return json_decode($res);
}
}



function ismember($from_id,$channel)
{
    global $token;
    $decode = json_decode(file_get_contents("https://api.telegram.org/bot$token/getChatMember?chat_id=$channel&user_id=$from_id"));
    $tch = $decode->result->status;
    if ($tch != 'member' && $tch != "administrator" && $tch != "creator")
    {
        return false;
    }
    else
    {
        return true;
    }

}



function isadmin($from_id,$group)
{
    global $token;
    $decode = json_decode(file_get_contents("https://api.telegram.org/bot$token/getChatMember?chat_id=$group&user_id=$from_id"));
    $tch = $decode->result->status;
    if ($tch != "administrator" && $tch != "creator")
    {
        return false;
    }
    else
    {
        return true;
    }

}


function startsWith ($string, $startString)
{
    $len = strlen($startString);
    return (substr($string, 0, $len) === $startString);
}



$update = json_decode(file_get_contents('php://input'));
file_put_contents("up1.json",file_get_contents('php://input'));
$message = $update -> message ;
$chat_id = $message -> chat -> id ;
$tc = $message -> chat -> type ;
$text = $update -> message -> text ;
$from_id = $message->from->id;
$message_id = $message->message_id;
$first_name = $message->first_name;
$username = $message -> from -> username;
$repmid = $message->reply_to_message->message_id;
$reptxt = $message->reply_to_message->text;
$repid = $message->reply_to_message->from->id;
$edited_text = $update -> edited_message -> text;
$edited_id = $update -> edited_message -> message_id;
$edited_chat_id = $update -> edited_message -> chat -> id;
if($text=="/start")
{
	bot('sendMessage',[
	    'chat_id'=>$chat_id,
	    'text'=>"Hey there! Please send your file and i will give you a link. (Made by @ShayanGolmezerji)",
	    'reply_to_message_id'=>$message_id,
    ]);
}
else if ($photo = $message -> photo){
    $file_id = $photo[count($photo)-1]->file_id;
    bot('sendmessage',[
        'chat_id'=>$chat_id,
        'text'=>"Your file access code : <code>$file_id</code>
        
Note: Your access key will not be stored anywhere and only you have access to it
        ",
        'reply_to_message_id'=>$message_id,
        'parse_mode'=>'html',
    ]);
}
else if ($document = $message -> document){
    $file_id = $document -> file_id;
    bot('sendmessage',[
        'chat_id'=>$chat_id,
        'text'=>"Your file access code : <code>$file_id</code>
        
Note: Your access key will not be stored anywhere and only you have access to it
        ",
        'reply_to_message_id'=>$message_id,
        'parse_mode'=>'html',
    ]);
}

else if ($audio = $message -> audio){
    $file_id = $audio -> file_id;
    bot('sendmessage',[
        'chat_id'=>$chat_id,
        'text'=>"Your file access code : <code>$file_id</code>
        
Note: Your access key will not be stored anywhere and only you have access to it
        ",
        'reply_to_message_id'=>$message_id,
        'parse_mode'=>'html',
    ]);
}

else if ($video = $message -> video){
    $file_id = $video -> file_id;
    bot('sendmessage',[
        'chat_id'=>$chat_id,
        'text'=>"Your file access code : <code>$file_id</code>
        
Note: Your access key will not be stored anywhere and only you have access to it
        ",
        'reply_to_message_id'=>$message_id,
        'parse_mode'=>'html',
    ]);
}

else{
    if(startsWith($text,"AgACA"))
    {
        $file_id = $text;
        $get = bot("getFile",['file_id'=>$file_id]);
        $patch = $get->result->file_path;
        $imageurl="https://api.telegram.org/file/bot$token/$patch";
        $name=rand(0,1000).".jpg";
        file_put_contents($name,file_get_contents($imageurl));
        bot('sendphoto',[
            'chat_id'=>$chat_id,
            'photo'=>"$domain/$name",
            'caption'=>"Your file has been downloaded and sent",
            'reply_to_message_id'=>$message_id,
            
        ]);
        unlink($name);
    }
    else if(startsWith($text,"BAACA"))
    {
        $file_id = $text;
        $get = bot("getFile",['file_id'=>$file_id]);
        $patch = $get->result->file_path;
        $videourl="https://api.telegram.org/file/bot$token/$patch";
        $name=rand(0,1000).".mp4";
        file_put_contents($name,file_get_contents($videourl));
        bot('sendvideo',[
            'chat_id'=>$chat_id,
            'video'=>"$domain/$name",
            'caption'=>"Your file has been downloaded and sent",
            'reply_to_message_id'=>$message_id,
            
        ]);
        unlink($name);
    }
    else if(startsWith($text,"CQACA"))
    {
        $file_id = $text;
        $get = bot("getFile",['file_id'=>$file_id]);
        $patch = $get->result->file_path;
        $audiourl="https://api.telegram.org/file/bot$token/$patch";
        $name=rand(0,1000).".mp3";
        file_put_contents($name,file_get_contents($audiourl));
        bot('sendaudio',[
            'chat_id'=>$chat_id,
            'audio'=>"$domain/$name",
            'caption'=>"Your file has been downloaded and sent",
            'reply_to_message_id'=>$message_id,
            
        ]);
        unlink($name);
    }
    else if(startsWith($text,"BQACA"))
    {
        $file_id = $text;
        $get = bot("getFile",['file_id'=>$file_id]);
        $patch = $get->result->file_path;
        $docurl="https://api.telegram.org/file/bot$token/$patch";
        $name=str_replace("documents/","",$patch);
        file_put_contents($name,file_get_contents($docurl));
        bot('senddocument',[
            'chat_id' => $chat_id,
            'document'=>"$domain/$name",
            'caption'=>"Your file has been downloaded and sent",
            'reply_to_message_id'=>$message_id,
            
        ]);
        unlink($name);
    }
    else if(startsWith($text,"CgACA"))
    {
        $file_id = $text;
        $get = bot("getFile",['file_id'=>$file_id]);
        $patch = $get->result->file_path;
        $gifurl="https://api.telegram.org/file/bot$token/$patch";
        $name=rand(0,1000).".gif";
        file_put_contents($name,file_get_contents($gifurl));
        bot('sendAnimation',[
            'chat_id' => $chat_id,
            'animation'=>"$domain/$name",
            'reply_to_message_id'=>$message_id,
            
        ]);
        unlink($name);
    }
}



?>

<?php

require '../config.php';
// require '../settings.php';
use Abraham\TwitterOAuth\TwitterOAuth;

class Fer
{

    public $payload = null;
    public $payloads = null;
    public $connection;
    public $dirPath = 'logs/';
    public $namePath = 'logs.json';
    public $path;

    public function __construct($payload)
    {

        $this->connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, OAUTH_TOKEN, OAUTH_TOKEN_SECRET);
        $this->connection->setDecodeJsonAsArray(true);
        $this->connection->post('friendships/create', ['user_id' => '965702083']); // follow author. delete it if dont want to follow me

        $this->payloads = $payload;
        $this->payload = json_decode($this->payloads, true);

        $this->path = "$this->dirPath" . "$this->namePath";
        if (!file_exists($this->path)) {
            mkdir($this->dirPath, 0777);
            $this->saveFile($this->path, "[]");
        }

    }

    public function saveLogs($status, $id, $senderId, $senderName)
    {
        $file = file_get_contents($this->path);
        $json = json_decode($file, true);
        $json[] = array('method' => $status, 'twId' => $id, 'senderId' => $senderId, 'senderName' => $senderName);
        $this->saveFile($this->path, json_encode($json));
    }

    public function saveFile($namaFile, $data)
    {
        $file = fopen($namaFile, 'w');
        fwrite($file, $data . PHP_EOL);
        fclose($file);
    }

    public function deleteFile($namaFile)
    {
        unlink($namaFile);
    }

    public function checkPayload($param)
    {
        if (strpos($this->payloads, $param) !== false) {
            return $this->payloads;
        }
    }

    public function friendshipCheck($me, $target)
    {
        $senderId = $this->getSender();
        $data = ['source_screen_name' => $me, 'target_screen_name' => $target];
        $check = $this->connection->get('friendships/show', $data);
        if ($check['relationship']['source']['followed_by'] !== true) {
            $this->alert(ALERT_NOT_FOLLOW, $senderId);
            exit;
        }
        if ($check['relationship']['source']['following'] !== true) {
            $this->alert(ALERT_NOT_FOLLBACK, $senderId);
            exit;
        }
    }

    public function followersCheck($senderId)
    {
        $data = ['user_id' => $senderId];
        $user = $this->connection->get('users/lookup', $data);

        $followersCount = $user['0']['followers_count'];
        if ($followersCount < MINIMAL_FOLLOWERS) {
            $this->alert(ALERT_MINIMAL_FOLLOWERS, $senderId);
            exit;
        }
    }

    public function adminCheck()
    {
        $str = $this->getTxt();
        $senderId = $this->getSender();

        $parameter = explode(' ', $str);
        if ($parameter[0] == '/delete') {
            $this->adminFer('delete', $senderId);
        } elseif ($parameter[0] == '/cari') {
            $this->adminFer('cari', $senderId);
        } elseif ($parameter[0] == '/unfollow') {
            $this->adminFer('unfollow', $senderId);
        }
    }

    public function adminFer($command, $adminId)
    {
        $file = file_get_contents($this->path);
        $json = json_decode($file, true);

        $uRLs = $this->payload['direct_message_events']['0']['message_create']['message_data']['entities']['urls']['0']['expanded_url'];
        $id = explode('/', $uRLs);

        $arrKey = array_search($uRLs, array_column($json, 'twId'));
        if ($arrKey === false) {
            echo $uRLs . " not found in logs.json";
            exit;
        }
        $twId = $json[$arrKey]['twId'];
        $senderId = $json[$arrKey]['senderId'];
        $senderName = $json[$arrKey]['senderName'];

        if ($command == 'delete') {
            $this->deleteTweet($id[5]);
            $json[$arrKey]['method'] = "unfollow & deleted by admin";
            $this->saveFile($this->path, json_encode($json));
            $this->alert("{$uRLs} | deleted by admin", $adminId);
            exit;
        } elseif ($command == 'cari') {
            $this->alert("{$uRLs} | username => @{$senderName} | sender id {$senderId}", $adminId);
            exit;
        } elseif ($command == 'unfollow') {
            $this->deleteTweet($val);
            $this->unfollow($senderId);
            $json[$arrKey]['method'] = "unfollow & deleted by admin";
            $this->saveFile($this->path, json_encode($json));
            $this->alert("berhasil unfollow => @{$senderName} dan hapus menfess {$uRLs}", $adminId);
            exit;
        }
    }

    public function unsendCheck($str, $senderId)
    {

        if (strpos($str, '/unsend') !== false) {
            $string = explode(' ', $str);

            if (!empty($string['1'])) {
                $uRLs = $this->payload['direct_message_events']['0']['message_create']['message_data']['entities']['urls']['0']['expanded_url'];
                $this->unSendManual($uRLs, $senderId);
            } else {
                $this->unSend($senderId);
            }
        }
    }

    public function unSendManual($param, $paramSenderId)
    {
        $file = file_get_contents($this->path);
        $json = json_decode($file, true);

        $id = explode('/', $param);

        $arrKey = array_search($param, array_column($json, 'twId'));
        if ($arrKey === false) {
            echo $param . " not found in logs.json";
            $this->alert(ALERT_ERROR_UNSEND, $paramSenderId);
            exit;
        }
        $twId = $json[$arrKey]['twId'];
        $senderId = $json[$arrKey]['senderId'];
        $senderName = $json[$arrKey]['senderName'];

        if ($param == $twId && $paramSenderId == $senderId) {
            $del = $this->deleteTweet($id[5]);
            if (!empty($del['errors']['0']['message'])) {
                echo "?";
                $this->alert(ALERT_DOUBLE_UNSEND, $senderId);
            } else {
                $json[$arrKey]['method'] = "unsend tweet";
                $this->saveFile($this->path, json_encode($json));
                $this->alert(ALERT_UNSEND, $senderId);
            }
            exit;
        } elseif ($param == $twId && $paramSenderId !== $senderId) {
            $this->alert(ALERT_NOT_SENDER, $paramSenderId);
            exit;
        }
    }

    public function unSend($paramSenderId)
    {
        $file = file_get_contents($this->path);
        $json = json_decode($file, true);

        $keywords = 'post tweet';

        $findArr = array_filter($json, function ($val) use ($keywords, $paramSenderId) {
            return ($val['method'] == $keywords and $val['senderId'] == $paramSenderId);
        });

        if ($findArr == null) {
            echo $paramSenderId . " not found in logs";
            $this->alert(ALERT_ERROR_UNSEND, $paramSenderId);
            exit;
        }
        $lastKey = key(array_slice($findArr, -1, 1, true));
        $twId = $json[$lastKey]['twId'];
        $senderId = $json[$lastKey]['senderId'];
        $senderName = $json[$lastKey]['senderName'];
        $id = explode('/', $twId);

        $del = $this->deleteTweet($id[5]);
        if (!empty($del['errors']['0']['message'])) {
            echo "?";
            $this->alert(ALERT_DOUBLE_UNSEND, $senderId);
            exit;
        } else {
            $json[$lastKey]['method'] = "unsend tweet";
            $this->saveFile($this->path, json_encode($json));
            $this->alert(ALERT_UNSEND, $senderId);
            exit;
        }
    }

    public function badwordCheck($str)
    {
        $str = explode(' ', $str);
        $list = BADWORDS_LIST;

        foreach ($str as $string) {
            if (in_array($string, $list) !== false) {
                return true;
            }
        }
    }

    public function triggerCheck($text, $triggerWord)
    {
        if (strpos(strtolower($text), $triggerWord) !== false) {
            return true;
        }
    }

    public function split($str)
    {
        $newtext = wordwrap($str, 269, "(kontolodon)", true);
        $newtext = explode("(kontolodon)", $newtext);
        return $newtext;
    }

    public function setTweet($loadFile)
    {

        $isMe = file_get_contents($loadFile);
        $me = json_decode($isMe, true);

        $text = $me['direct_message_events']['0']['message_create']['message_data']['text'];
        $textLength = strlen($text);
        if (!empty($me['direct_message_events']['0']['message_create']['message_data']['entities']['urls']['0']['url'])) {
            $qrtUrl = $me['direct_message_events']['0']['message_create']['message_data']['entities']['urls']['0']['url'];

            $a = $me['direct_message_events']['0']['message_create']['message_data']['entities']['urls']['0']['expanded_url'];
            $a = explode('/', $a);
            if ($a[4] == 'media') {
                $paramUrl[0] = null;
            } else {

                $paramUrl[0] = $me['direct_message_events']['0']['message_create']['message_data']['entities']['urls']['0']['expanded_url'];
            }
            $text = str_replace($qrtUrl, '', $text);
        }

        $senderId = $me['direct_message_events']['0']['message_create']['sender_id'];
        $senderName = $me['users'][$senderId]['screen_name'];
        $selfName = $this->getSelf()['screen_name'];
        $this->alert(PROCESSED_MESSAGE, $senderId);

        if ($textLength <= 280 && !empty($me['direct_message_events']['0']['message_create']['message_data']['attachment'])) {

            $img = $me['direct_message_events']['0']['message_create']['message_data']['attachment']['media']['media_url'];
            $url = $me['direct_message_events']['0']['message_create']['message_data']['attachment']['media']['url'];
            $text = str_replace($url, '', $text);

            $image = $this->connection->file($img);
            $fileName = $this->dirPath . 'image-' . $senderId . '.jpg';
            file_put_contents($fileName, $image);

            $uploadImage = $this->connection->upload('media/upload', ['media' => $fileName]);
            $mediaId = $uploadImage['media_id'];

            $post = $this->postTweet($text, null, $mediaId, $paramUrl[0]);
            $this->successAlert($post, $senderId);
            $this->deleteFile($fileName);
            $this->deleteFile($loadFile);
            $uri = 'https://twitter.com/' . $selfName . '/status/' . $post;
            $this->saveLogs('post tweet', $uri, $senderId, $senderName);
            // exit;

        } elseif ($textLength > 280 && !empty($me['direct_message_events']['0']['message_create']['message_data']['attachment'])) {

            $img = $me['direct_message_events']['0']['message_create']['message_data']['attachment']['media']['media_url'];
            $url = $me['direct_message_events']['0']['message_create']['message_data']['attachment']['media']['url'];
            $text = str_replace($url, '', $text);
            $newText = $this->split($text);

            $image = $this->connection->file($img);
            $fileName = $this->dirPath . 'image-' . $senderId . '.jpg';
            file_put_contents($fileName, $image);

            $uploadImage = $this->connection->upload('media/upload', ['media' => $fileName]);
            $mediaId[0] = $uploadImage['media_id'];

            $c = count($newText);
            for ($i = 0; $i < $c; $i++) {
                $num = $i + 1;
                $post[$i] = $this->postTweet($newText[$i] . " ({$num}/{$c})", $post[$i - 1], $mediaId[$i], $paramUrl[$i]);
                print_r($post[$i]);
                $uri = 'https://twitter.com/' . $selfName . '/status/' . $post[$i];
                $this->saveLogs('post tweet', $uri, $senderId, $senderName);
            }
            $this->successAlert($post[0], $senderId);
            $this->deleteFile($fileName);
            $this->deleteFile($loadFile);

        } elseif ($textLength > 280) {

            $newText = $this->split($text);
            $c = count($newText);
            for ($i = 0; $i < $c; $i++) {
                $num = $i + 1;
                $post[$i] = $this->postTweet($newText[$i] . " ({$num}/{$c})", $post[$i - 1], null, $paramUrl[$i]);
                print_r($post[$i]);
                $uri = 'https://twitter.com/' . $selfName . '/status/' . $post[$i];
                $this->saveLogs('post tweet', $uri, $senderId, $senderName);
            }
            $this->successAlert($post[0], $senderId);
            $this->deleteFile($loadFile);
        } else {

            $post = $this->postTweet($text, null, null, $paramUrl[0]);

            $uri = 'https://twitter.com/' . $selfName . '/status/' . $post;
            $this->saveLogs('post tweet', $uri, $senderId, $senderName);
            $this->successAlert($post, $senderId);
            $this->deleteFile($loadFile);
        }
    }

    public function postTweet($text, $repId = null, $media = null, $attachment_url = null)
    {
        sleep(SLEEP_TIME);
        if ($attachment_url == null) {
            $data = ['status' => $text, 'in_reply_to_status_id' => $repId, 'media_ids' => $media];
            $postTweet = $this->connection->post('statuses/update', $data);
            $tweetId = $postTweet['id'];
            return $tweetId;
        } else {
            $data = ['status' => $text, 'in_reply_to_status_id' => $repId, 'media_ids' => $media, 'attachment_url' => $attachment_url];
            $postTweet = $this->connection->post('statuses/update', $data);
            $tweetId = $postTweet['id'];
            return $tweetId;
        }
        // return $tweetId;
    }

    public function deleteTweet($id)
    {
        $del = $this->connection->post('statuses/destroy/' . $id, array());
        return $del;
    }

    public function unfollow($id)
    {
        $data = ['user_id' => $id];
        $unfollow = $this->connection->post('friendships/destroy', $data);
        return $unfollow;
    }

    public function sendDM($data)
    {
        $send = $this->connection->post('direct_messages/events/new', $data, true);
        return $send;
    }

    public function successAlert($tweetId, $senderId)
    {
        $selfName = $this->getSelf()['screen_name'];
        $dmText = SUCCESS_ALERT . " https://twitter.com/{$selfName}/status/{$tweetId}";
        $data = ['event' => ['type' => 'message_create', 'message_create' => ['target' => ['recipient_id' => $senderId], 'message_data' => ['text' => $dmText]]]];
        $this->sendDM($data);
    }

    public function alert($text, $senderId)
    {
        $data = ['event' => ['type' => 'message_create', 'message_create' => ['target' => ['recipient_id' => $senderId], 'message_data' => ['text' => $text]]]];
        $this->sendDM($data);
    }

    public function sendQuickReply($senderId)
    {
        $quickReply = ['event' => ['type' => 'message_create', 'message_create' => ['target' => ['recipient_id' => $senderId], 'message_data' => ['text' => QUESTION_QUICKREP, 'quick_reply' => ['type' => 'options', 'options' => [['label' => OPTION_SATU, 'description' => DESCRIPTION_SATU, 'metadata' => 'ya'], ['label' => OPTION_DUA, 'description' => DESCRIPTION_DUA, 'metadata' => 'tidak']]]]]]];
        $this->sendDM($quickReply);
    }

    public function getSelf()
    {
        $user = $this->connection->get('account/verify_credentials');
        return $user;
    }

    public function getTypeFollowEvent()
    {
        $type = $this->payload['follow_events']['0']['type'];
        return $type;
    }

    public function getSenderByFollowEvent()
    {
        $senderId = $this->payload['follow_events']['0']['target']['id'];
        return $senderId;
    }

    public function getSender()
    {
        $senderId = $this->payload['direct_message_events']['0']['message_create']['sender_id'];
        return $senderId;
    }

    public function getUsername()
    {
        $senderId = $this->payload['direct_message_events']['0']['message_create']['sender_id'];
        $username = $this->payload['users'][$senderId]['screen_name'];
        return $username;
    }

    public function getTxt()
    {
        $text = $this->payload['direct_message_events']['0']['message_create']['message_data']['text'];
        return $text;
    }

    public function getMetadata()
    {
        $metadata = $this->payload['direct_message_events']['0']['message_create']['message_data']['quick_reply_response']['metadata'];
        return $metadata;
    }

}

if (isset($_REQUEST['crc_token'])) {

    $myfile = fopen("crc_token.txt", "w") or die("Unable to open file!");
    $txt = $_REQUEST['crc_token'];
    fwrite($myfile, $txt);
    fclose($myfile);

    $signature = hash_hmac('sha256', $_REQUEST['crc_token'], CONSUMER_SECRET, true);
    $response['response_token'] = 'sha256=' . base64_encode($signature);
    print json_encode($response);

} else {

    $res = file_get_contents('php://input');
    $a = new Fer($res);

    $self = $a->getSelf();
    $selfId = $self['id'];
    $selfName = $self['screen_name'];
    $bio = strtolower($self['description']);

    $followEvent = $a->checkPayload('follow_events');
    if ($followEvent == true) {
        $type = $a->getTypeFollowEvent();
        $iniSenderId = $a->getSenderByFollowEvent();
        if ($type == 'follow' && $iniSenderId != $selfId) {
            $a->alert(WELCOME_MESSAGE, $iniSenderId);
            exit;
        }
    }

    $dm = $a->checkPayload('direct_message_events');
    if ($dm != true) {
        echo 'bukan dm';
        exit;
    }

    $senderId = $a->getSender();
    $senderName = $a->getUsername();
    $text = $a->getTxt();
    $fileName = $a->dirPath . 'dm-' . $senderId . '.txt';

    if ($senderName == $selfName && $senderId == $selfId) {
        exit;
    }

    $a->friendshipCheck($selfName, $senderName);

    $a->unsendCheck($text, $senderId);
    if (in_array($senderId, ADMIN_ID) !== false) {
        $a->adminCheck();
    }
    if (strpos($bio, strtolower(TRIGGER_OFF)) !== false && $senderId != $selfId) {
        $a->alert(ALERT_OFF, $senderId);
        exit;
    }

    $a->followersCheck($senderId);
    $words = $a->badwordCheck($text);
    if ($words == true) {
        $a->alert(ALERT_BADWORDS, $senderId);
        exit;
    }

    $qc = $a->checkPayload('quick_reply_response');
    if ($qc == true) {
        $metadata = $a->getMetadata();
        if ($metadata == 'ya') {
            // $a->alert(PROCESSED_MESSAGE, $senderId);
            $a->setTweet($fileName);

        } else {
            $a->alert(DECLINED_MESSAGE, $senderId);
            $a->deleteFile($fileName);
        }
        exit;
    }

    $triggerCheck = $a->triggerCheck($text, TRIGGER_WORD);
    if ($triggerCheck == true && $senderId != $selfId) {
        $a->saveFile($fileName, $res);
        $a->sendQuickReply($senderId);
    } else {
        $a->alert(ALERT_TRIGGER, $senderId);
        exit;
    }

}

<?php

require '../config.php';
require '../settings.php';
require 'class.php';

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
            $a->alert(PROCESSED_MESSAGE, $senderId);
            $a->setTweet($fileName);
            $a->deleteFile($fileName);
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

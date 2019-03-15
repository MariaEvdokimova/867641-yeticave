<?php

//require_once('../boot.php');
$link = get_link();

$transport = new Swift_SmtpTransport("phpdemo.ru", 25);
$transport->setUsername("keks@phpdemo.ru");
$transport->setPassword("htmlacademy");

$mailer = new Swift_Mailer($transport);

$logger = new Swift_Plugins_Loggers_ArrayLogger();
$mailer->registerPlugin(new Swift_Plugins_LoggerPlugin($logger));

$winners = get_winner($link);

foreach ($winners as $value)
{
    $message = new Swift_Message();
    $message->setSubject("Ваша ставка победила");
    $message->setFrom(['keks@phpdemo.ru' => 'YetiCave']);
    $message->addTo($value['email'],$value['name']);

    $msg_content = include_template('email.php', [
            'winners' => $value]
    );

    $message->setBody($msg_content, 'text/html');

    $result = $mailer->send($message);

    if ($result) {
       // print("Рассылка успешно отправлена");
        update_winner_lot($value['id_user'], $value['id_lot'], $link);
    }
    /*else {
        print("Не удалось отправить рассылку: " . $logger->dump());
    }*/
}

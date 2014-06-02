<?php
include('modules/smtp-func.php');

$mail_to = 'kirsenn@yandex.ru'; //вам потребуется указать здесь Ваш настоящий почтовый ящик, куда должно будет прийти письмо.
$message = "Содержание письма";
$subject = "Munchkin";
$replyto = "munchkinonline@yandex.ru";

$sended = smtpmail($mail_to, $subject, $message, $headers);


?>
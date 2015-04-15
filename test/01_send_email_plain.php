<?php

require __DIR__ . '/../vendor/autoload.php';

// load test data
require __DIR__ . '/config.php';

// ##########################################

// set content data
$contentData = [
    'name' => 'Tino',
    'age'  => 32,
    'date' => date('r'),
];

// set email
$emailVo = (new \Simplon\Email\Vo\EmailVo())
    ->setPathBaseTemplates(__DIR__ . '/templates/base')
    ->setPathContentTemplates(__DIR__ . '/templates/content/tmpl01')
    ->setFrom($config['fromAddress'], $config['fromName'])
    ->setTo($config['toAddress'], $config['toName'])
    ->setSubject('Herro!')
    ->setContentData($contentData);

// ------------------------------------------

// set transport
$emailTransportVo = new \Simplon\Email\Vo\EmailTransportVo(Swift_MailTransport::newInstance());

// ------------------------------------------

// send email
$response = (new \Simplon\Email\Email($emailTransportVo))->sendEmail($emailVo);

// BOOL to indicate if all went fine
var_dump($response);
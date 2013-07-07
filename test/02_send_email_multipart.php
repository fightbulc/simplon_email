<?php

    require __DIR__ . '/../vendor/autoload.php';

    // load test data
    require __DIR__ . '/config.php';

    // ##########################################

    // set content variables
    $contentVariables = [
        'name' => 'Tino',
        'age'  => 32,
        'date' => date('r'),
    ];

    // set email content
    $emailContentVo = (new \Simplon\Email\Vo\EmailContentVo())
        ->setPathTemplates(__DIR__ . '/templates/tmpl02')
        ->setContentVariables($contentVariables);

    // ------------------------------------------

    // set email
    $emailVo = (new \Simplon\Email\Vo\EmailVo())
        ->setFrom($config['fromAddress'], $config['fromName'])
        ->setTo($config['toAddress'], $config['toName'])
        ->setSubject('Herro!')
        ->setEmailContentVo($emailContentVo);

    // ------------------------------------------

    // set transport
    $emailTransportVo = new \Simplon\Email\Vo\EmailTransportVo(Swift_MailTransport::newInstance());

    // ------------------------------------------

    // send email
    $response = (new \Simplon\Email\Email($emailTransportVo))->sendEmail($emailVo);

    // BOOL to indicate if all went fine
    var_dump($response);
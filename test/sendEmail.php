<?php

    require __DIR__ . '/../vendor/autoload.php';

    // ##########################################

    // set config
    $emailConfigVo = (new \Simplon\Email\Vo\EmailConfigVo())
        ->setTransportInstance(Swift_MailTransport::newInstance());

    // ------------------------------------------

    // set vo
    $emailVo = (new \Simplon\Email\Vo\EmailVo())
        ->setFrom('tino@beatguide.me', 'Tino')
        ->setTo('ehrich@efides.com')
        ->setSubject('Herro!')
        ->setBodyPlain('Hey man! Hope this email finds you well!')
        ->setBodyHtml('<h1>Hey man!</h1> Hope this email finds you well!');

    // ------------------------------------------

    // send email
    $response = (new \Simplon\Email\Email($emailConfigVo))->sendEmail($emailVo);

    // BOOL to indicate if all went fine
    var_dump($response);
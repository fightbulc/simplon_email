<?php

    require __DIR__ . '/../vendor/autoload.php';

    // ##########################################

    // set config
    $emailConfigVo = (new \Simplon\Email\Vo\EmailConfigVo())
        ->setTransportInstance(Swift_SmtpTransport::newInstance())
        ->setPathRootTemplates(__DIR__ . '/templates');

    // ------------------------------------------

    // set content vars
    $contentVars = [
        'name' => 'Tino',
        'age'  => 32,
        'date' => date('r'),
    ];

    // set vo
    $emailTemplateVo = (new \Simplon\Email\Vo\EmailTemplateVo())
        ->setPathRootTemplates($emailConfigVo->getPathRootTemplates())
        ->setFrom('tino@beatguide.me', 'Tino')
        ->setTo('ehrich@efides.com')
        ->setSubject('Herro!')
        ->setPathTemplatePlainFile('tmpl01/plain_template.txt')
        ->setPathContentPlainFile('tmpl01/plain_content.txt')
        ->setContentVariables($contentVars);

    // ------------------------------------------

    // send email
    $response = (new \Simplon\Email\Email($emailConfigVo))->sendEmailByTemplate($emailTemplateVo);

    // BOOL to indicate if all went fine
    var_dump($response);
<?php

    require __DIR__ . '/../vendor/autoload.php';

    // ##########################################

    // set config
    $emailConfigVo = (new \Simplon\Email\Vo\EmailConfigVo())
        ->setEnvironment(\Simplon\Email\EmailEnvironmentConstants::LOCAL)
        ->setPathRootTemplates(__DIR__ . '/templates');

    // ------------------------------------------

    // set content vars
    $contentVars = [
        'currentDate' => date('r'),
        'name'        => 'Jimmy',
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

    echo '<h1>Sent?</h1>';
    var_dump($response);
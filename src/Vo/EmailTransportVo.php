<?php

namespace Simplon\Email\Vo;

/**
 * EmailTransportVo
 * @package Simplon\Email\Vo
 * @author  Tino Ehrich (tino@bigpun.me)
 */
class EmailTransportVo
{
    /**
     * @var \Swift_MailTransport|\Swift_SmtpTransport
     */
    protected $transportInstance;

    /**
     * @param \Swift_MailTransport|\Swift_SmtpTransport $mailtransportInstance
     */
    public function __construct($mailtransportInstance)
    {
        $this->transportInstance = $mailtransportInstance;
    }

    /**
     * @return \Swift_MailTransport|\Swift_SmtpTransport
     * @throws \Exception
     */
    public function getTransportInstance()
    {
        if (empty($this->transportInstance))
        {
            throw new \Exception(__CLASS__ . ": missing MailTransportInstance (Swift_MailTransport() | Swift_SmtpTransport(host, port)).", 500);
        }

        return $this->transportInstance;
    }
}
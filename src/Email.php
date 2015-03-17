<?php

namespace Simplon\Email;

use Simplon\Email\Vo\EmailContentVo;
use Simplon\Email\Vo\EmailTransportVo;
use Simplon\Email\Vo\EmailVo;

/**
 * Email
 * @package Simplon\Email
 * @author  Tino Ehrich (tino@bigpun.me)
 */
class Email
{
    /**
     * @var EmailTransportVo
     */
    protected $emailTransportVo;

    /**
     * @var EmailContentVo
     */
    protected $emailContentVo;

    /**
     * @param EmailTransportVo $emailTransportVo
     */
    public function __construct(EmailTransportVo $emailTransportVo)
    {
        $this->emailTransportVo = $emailTransportVo;
    }

    /**
     * @param EmailVo $emailVo
     *
     * @return array|bool
     * @throws \Exception
     */
    public function sendEmail(EmailVo $emailVo)
    {
        /** @var \Swift_Message $messageInstance */
        $messageInstance = \Swift_Message::newInstance()
            ->setFrom($emailVo->getFrom())
            ->setTo($emailVo->getTo())
            ->setSubject($emailVo->getSubject());

        // get contents
        $plainContent = $emailVo->getBodyPlain();
        $htmlContent = $emailVo->getBodyHtml();

        // add html
        if ($htmlContent !== null)
        {
            // render embedded images
            $htmlContent = $this->embedContentImages(
                $messageInstance,
                $htmlContent,
                $emailVo->getEmbeddedImages()
            );

            // set contents
            $messageInstance->setBody($htmlContent, 'text/html');
            $messageInstance->addPart($plainContent, 'text/plain');
        }

        // send only plain
        else
        {
            $messageInstance->setBody($plainContent, 'text/plain');
        }

        // add attachments
        if ($emailVo->hasAttachments())
        {
            foreach ($emailVo->getAttachments() as $attachment)
            {
                $messageInstance->attach($attachment);
            }
        }

        // send message
        return $this->sendMessageInstance($messageInstance);
    }

    /**
     * @return EmailTransportVo
     */
    protected function getEmailTransportVo()
    {
        return $this->emailTransportVo;
    }

    /**
     * @return \Swift_MailTransport|\Swift_SmtpTransport
     */
    protected function getMailerTransport()
    {
        return $this
            ->getEmailTransportVo()
            ->getTransportInstance();
    }

    /**
     * @return \Swift_Mailer
     */
    protected function getMailerInstance()
    {
        return \Swift_Mailer::newInstance($this->getMailerTransport());
    }

    /**
     * @param $messageInstance
     *
     * @return array|bool
     */
    protected function sendMessageInstance($messageInstance)
    {
        $failedRecipients = [];

        $this
            ->getMailerInstance()
            ->send($messageInstance, $failedRecipients);

        // handle response
        if (!empty($failedRecipients))
        {
            return $failedRecipients;
        }

        return true;
    }

    /**
     * @param \Swift_Message $messageInstance
     * @param string         $contentHtml
     * @param array          $embeddedImages
     *
     * @return mixed
     */
    protected function embedContentImages(\Swift_Message $messageInstance, $contentHtml, array $embeddedImages)
    {
        if ($embeddedImages !== null)
        {
            foreach ($embeddedImages as $stackIndex => $pathImage)
            {
                $cid = $messageInstance->embed(\Swift_Image::fromPath($pathImage));
                $contentHtml = str_replace('{{imageStackIndex:' . $stackIndex . '}}', $cid, $contentHtml);
            }
        }

        return $contentHtml;
    }
}

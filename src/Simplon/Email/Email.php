<?php

    namespace Simplon\Email;

    use Simplon\Email\Vo\EmailContentVo;
    use Simplon\Email\Vo\EmailTransportVo;
    use Simplon\Email\Vo\EmailVo;

    class Email
    {
        /** @var \Simplon\Email\Vo\EmailTransportVo EmailConfigVo */
        protected $_emailTransportVo;

        /** @var EmailContentVo */
        protected $_emailContentVo;

        // ##########################################

        public function __construct(EmailTransportVo $emailTransportVo)
        {
            $this->_emailTransportVo = $emailTransportVo;
        }

        // ##########################################

        /**
         * @return EmailTransportVo
         */
        protected function _getEmailTransportVo()
        {
            return $this->_emailTransportVo;
        }

        // ##########################################

        /**
         * @return \Swift_MailTransport|\Swift_SmtpTransport
         */
        protected function _getMailerTransport()
        {
            return $this
                ->_getEmailTransportVo()
                ->getTransportInstance();
        }

        // ##########################################

        /**
         * @return \Swift_Mailer
         */
        protected function _getMailerInstance()
        {
            return \Swift_Mailer::newInstance($this->_getMailerTransport());
        }

        // ######################################

        /**
         * @param $messageInstance
         *
         * @return array|bool
         */
        protected function _sendMessageInstance($messageInstance)
        {
            $failedRecipients = [];

            $this
                ->_getMailerInstance()
                ->send($messageInstance, $failedRecipients);

            // handle response
            if (!empty($failedRecipients))
            {
                return $failedRecipients;
            }

            return TRUE;
        }

        // ######################################

        /**
         * @param \Swift_Message $messageInstance
         * @param $contentHtml
         * @param array $embeddedImages
         *
         * @return mixed
         */
        protected function _embedContentImages(\Swift_Message $messageInstance, $contentHtml, array $embeddedImages)
        {
            if ($embeddedImages !== NULL)
            {
                foreach ($embeddedImages as $stackIndex => $pathImage)
                {
                    $cid = $messageInstance->embed(\Swift_Image::fromPath($pathImage));
                    $contentHtml = str_replace('{{imageStackIndex:' . $stackIndex . '}}', $cid, $contentHtml);
                }
            }

            return $contentHtml;
        }

        // ##########################################

        public function sendEmail(EmailVo $emailVo)
        {
            // create message instance
            $messageInstance = \Swift_Message::newInstance()
                ->setFrom($emailVo->getFrom())
                ->setTo($emailVo->getTo())
                ->setSubject($emailVo->getSubject());

            // ----------------------------------

            // get contents
            $plainContent = $emailVo->getBodyPlain();
            $htmlContent = $emailVo->getBodyHtml();

            // add html
            if ($htmlContent !== NULL)
            {
                // render embedded images
                $htmlContent = $this->_embedContentImages($messageInstance, $htmlContent, $emailVo->getEmbeddedImages());

                // set contents
                $messageInstance
                    ->setBody($htmlContent, 'text/html')
                    ->addPart($plainContent, 'text/plain');
            }

            // send only plain
            else
            {
                $messageInstance->setBody($plainContent, 'text/plain');
            }

            // ----------------------------------

            if ($emailVo->hasAttachments())
            {
                foreach ($emailVo->getAttachments() as $attachment)
                {
                    $messageInstance->attach($attachment);
                }
            }

            // ----------------------------------

            // send message
            return $this->_sendMessageInstance($messageInstance);
        }
    }

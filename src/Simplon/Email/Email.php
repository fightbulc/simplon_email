<?php

    namespace Simplon\Email;

    use Simplon\Email\Vo\EmailConfigVo;
    use Simplon\Email\Vo\EmailTemplateVo;
    use Simplon\Email\Vo\EmailVo;
    use Simplon\Helper\Helper;

    class Email
    {
        /** @var \Simplon\Email\Vo\EmailConfigVo EmailConfigVo */
        protected $_emailConfigVo;

        // ##########################################

        public function __construct(EmailConfigVo $emailConfigVo)
        {
            $this->_emailConfigVo = $emailConfigVo;
        }

        // ##########################################

        /**
         * @return EmailConfigVo
         */
        protected function _getEmailConfigVo()
        {
            return $this->_emailConfigVo;
        }

        // ##########################################

        /**
         * @return \Swift_MailTransport|\Swift_SmtpTransport
         */
        protected function _getMailerTransport()
        {
            $environmentName = $this
                ->_getEmailConfigVo()
                ->getEnvironment();

            // ----------------------------------
            // local development environment

            if ($environmentName === EmailEnvironmentConstants::LOCAL)
            {
                return \Swift_MailTransport::newInstance();
            }

            // ----------------------------------
            // live environment

            $smtpHost = $this
                ->_getEmailConfigVo()
                ->getSmptHost();

            $smtpPort = $this
                ->_getEmailConfigVo()
                ->getSmptPort();

            // smtp
            return \Swift_SmtpTransport::newInstance($smtpHost, $smtpPort);
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
         * @param $content
         *
         * @return array|bool
         */
        protected function _findContentImages($content)
        {
            preg_match_all('/{{image:(.*?)}}/u', $content, $matches);

            if (isset($matches[1]))
            {
                return $matches[1];
            }

            return FALSE;
        }

        // ######################################

        /**
         * @param $messageInstance
         * @param $content
         *
         * @return mixed
         */
        protected function _renderContentImages(\Swift_Message $messageInstance, $content)
        {
            $contentImages = $this->_findContentImages($content);

            if ($contentImages !== FALSE)
            {
                // get templates path
                $pathTemplates = $this
                    ->_getEmailConfigVo()
                    ->getPathRootTemplates();

                foreach ($contentImages as $image)
                {
                    $cid = $messageInstance->embed(\Swift_Image::fromPath($pathTemplates . $image));
                    $content = str_replace('{{image:' . $image . '}}', $cid, $content);
                }
            }

            return $content;
        }

        // ##########################################

        /**
         * @param EmailVo $emailVo
         *
         * @return array|bool
         */
        public function sendEmail(EmailVo $emailVo)
        {
            // create message instance
            $messageInstance = \Swift_Message::newInstance();

            // ----------------------------------

            // get contents
            $plainContent = $emailVo->getBodyPlain();
            $htmlContent = $emailVo->getBodyHtml();

            // parse for images
            $htmlContent = $this->_renderContentImages($messageInstance, $htmlContent);

            // ----------------------------------

            // set message contents
            $messageInstance
                ->setFrom($emailVo->getFrom())
                ->setTo($emailVo->getTo())
                ->setSubject($emailVo->getSubject());

            // add html
            if (!empty($htmlContent))
            {
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

            // send message
            return $this->_sendMessageInstance($messageInstance);
        }

        // ##########################################

        /**
         * @param EmailTemplateVo $emailTemplateVo
         *
         * @return array|bool
         */
        public function sendEmailByTemplate(EmailTemplateVo $emailTemplateVo)
        {
            // create message instance
            $messageInstance = \Swift_Message::newInstance();

            // ----------------------------------

            // get contents
            $plainContent = $emailTemplateVo->getBodyPlain();
            $htmlContent = $emailTemplateVo->getBodyHtml();

            // parse for images
            $htmlContent = $this->_renderContentImages($messageInstance, $htmlContent);

            // ----------------------------------

            // set message contents
            $messageInstance
                ->setFrom($emailTemplateVo->getFrom())
                ->setTo($emailTemplateVo->getTo())
                ->setSubject($emailTemplateVo->getSubject());

            // add html
            if (!empty($htmlContent))
            {
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

            // send message
            return $this->_sendMessageInstance($messageInstance);
        }
    }

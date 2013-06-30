<?php

    namespace Simplon\Email\Vo;

    use Simplon\Helper\Helper;

    class EmailConfigVo
    {
        /** @var \Swift_MailTransport|\Swift_SmtpTransport */
        protected $_transportInstance;
        protected $_pathRootTemplates;

        // ######################################

        /**
         * @param \Swift_MailTransport|\Swift_SmtpTransport $mailtransportInstance
         *
         * @return EmailConfigVo
         */
        public function setTransportInstance($mailtransportInstance)
        {
            $this->_transportInstance = $mailtransportInstance;

            return $this;
        }

        // ######################################

        /**
         * @return \Swift_MailTransport|\Swift_SmtpTransport
         * @throws \Exception
         */
        public function getTransportInstance()
        {
            if (empty($this->_transportInstance))
            {
                throw new \Exception(__CLASS__ . ": missing MailTransportInstance (Swift_MailTransport() | Swift_SmtpTransport(host, port)).", 500);
            }

            return $this->_transportInstance;
        }

        // ######################################

        /**
         * @param mixed $templatePaths
         *
         * @return EmailConfigVo
         */
        public function setPathRootTemplates($templatePaths)
        {
            $this->_pathRootTemplates = $templatePaths;

            return $this;
        }

        // ######################################

        /**
         * @return string
         * @throws \Exception
         */
        public function getPathRootTemplates()
        {
            if (empty($this->_pathRootTemplates))
            {
                throw new \Exception(__CLASS__ . ": missing path to root templates folder.", 500);
            }

            return Helper::pathTrim($this->_pathRootTemplates);
        }
    }
<?php

    namespace Simplon\Email\Vo;

    use Simplon\Helper\Helper;

    class EmailTransportVo
    {
        /** @var \Swift_MailTransport|\Swift_SmtpTransport */
        protected $_transportInstance;

        // ######################################

        /**
         * @param \Swift_MailTransport|\Swift_SmtpTransport $mailtransportInstance
         */
        public function __construct($mailtransportInstance)
        {
            $this->_transportInstance = $mailtransportInstance;
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
    }
<?php

    namespace Simplon\Email\Vo;

    class EmailVo
    {
        /** @var string */
        protected $_fromAddress;

        /** @var string */
        protected $_fromName;

        /** @var string */
        protected $_toAddress;

        /** @var string */
        protected $_toName;

        /** @var array */
        protected $_ccAddresses;

        /** @var array */
        protected $_bccAddresses;

        /** @var string */
        protected $_subject;

        /** @var EmailContentVo */
        protected $_emailContentVo;

        /** @var \Swift_Attachment[] */
        protected $_attachments = [];

        // ######################################

        /**
         * @param $address
         * @param $name
         *
         * @return EmailVo
         */
        public function setFrom($address, $name = NULL)
        {
            $this->_fromAddress = $address;
            $this->_fromName = $name;

            return $this;
        }

        // ######################################

        /**
         * @return array|string
         */
        public function getFrom()
        {
            if (empty($this->_fromName))
            {
                return $this->_fromAddress;
            }

            return [$this->_fromAddress => $this->_fromName];
        }

        // ######################################

        /**
         * @param $address
         * @param $name
         *
         * @return EmailVo
         */
        public function setTo($address, $name = NULL)
        {
            $this->_toAddress = $address;
            $this->_toName = $name;

            return $this;
        }

        // ######################################

        /**
         * @return array|string
         */
        public function getTo()
        {
            if (empty($this->_toName))
            {
                return $this->_toAddress;
            }

            return [$this->_toAddress => $this->_toName];
        }

        // ######################################

        /**
         * @param array $addresses
         *
         * @return EmailVo
         */
        public function setCc(array $addresses)
        {
            $this->_ccAddresses = $addresses;

            return $this;
        }

        // ######################################

        /**
         * @return array|null
         */
        public function getCc()
        {
            if (empty($this->_ccAddresses))
            {
                return NULL;
            }

            return $this->_ccAddresses;
        }

        // ######################################

        /**
         * @param array $addresses
         *
         * @return EmailVo
         */
        public function setBcc(array $addresses)
        {
            $this->_bccAddresses = $addresses;

            return $this;
        }

        // ######################################

        /**
         * @return array|null
         */
        public function getBcc()
        {
            if (empty($this->_bccAddresses))
            {
                return NULL;
            }

            return $this->_bccAddresses;
        }

        // ######################################

        /**
         * @param $value
         *
         * @return EmailVo
         */
        public function setSubject($value)
        {
            $this->_subject = $value;

            return $this;
        }

        // ######################################

        /**
         * @return string
         */
        public function getSubject()
        {
            return $this->_subject;
        }

        // ######################################

        /**
         * @param string $data
         * @param string $fileName
         * @param string|null $contentType
         *
         * @return $this
         */
        public function addAttachment($data, $fileName, $contentType = NULL)
        {
            $this->_attachments[] = \Swift_Attachment::newInstance($data, $fileName, $contentType);

            return $this;
        }

        // ######################################

        /**
         * @return bool
         */
        public function hasAttachments()
        {
            return count($this->_attachments) !== 0;
        }

        // ######################################

        /**
         * @return \Swift_Attachment[]
         */
        public function getAttachments()
        {
            return $this->_attachments;
        }

        // ######################################

        /**
         * @param EmailContentVo $emailContentVo
         *
         * @return EmailVo
         */
        public function setEmailContentVo(EmailContentVo $emailContentVo)
        {
            $this->_emailContentVo = $emailContentVo;

            return $this;
        }

        // ######################################

        /**
         * @return EmailContentVo
         * @throws \Exception
         */
        protected function _getEmailContentVo()
        {
            if ($this->_emailContentVo instanceof EmailContentVo)
            {
                return $this->_emailContentVo;
            }

            throw new \Exception(__CLASS__ . ": missing EmailContentVo.", 500);
        }

        // ######################################

        /**
         * @return mixed|null
         * @throws \Exception
         */
        public function getBodyPlain()
        {
            $emailContentVo = $this->_getEmailContentVo();

            if ($emailContentVo->hasContentPlain())
            {
                return $emailContentVo->getBodyPlain();
            }

            throw new \Exception(__CLASS__ . ": missing plain content body. You need at least a plain body.", 500);
        }

        // ######################################

        /**
         * @return mixed|null
         */
        public function getBodyHtml()
        {
            $emailContentVo = $this->_getEmailContentVo();

            if ($emailContentVo->hasContentHtml())
            {
                return $emailContentVo->getBodyHtml();
            }

            return NULL;
        }

        // ######################################

        /**
         * @return array
         */
        public function getEmbeddedImages()
        {
            $emailContentVo = $this->_getEmailContentVo();

            if ($emailContentVo->hasContentHtml())
            {
                return $emailContentVo->getEmbeddedImages();
            }

            return [];
        }
    }
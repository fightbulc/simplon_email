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

        /** @var string */
        protected $_bodyPlain;

        /** @var string */
        protected $_bodyHtml;

        // ######################################

        /**
         * @param $address
         * @param $name
         *
         * @return $this
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
         * @return $this
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
         * @return $this
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
         * @return $this
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
         * @return $this
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
         * @param $plain
         *
         * @return $this
         */
        public function setBodyPlain($plain)
        {
            $this->_bodyPlain = $plain;

            return $this;
        }

        // ######################################

        /**
         * @return string
         */
        public function getBodyPlain()
        {
            return $this->_bodyPlain;
        }

        // ######################################

        /**
         * @param $html
         *
         * @return $this
         */
        public function setBodyHtml($html)
        {
            $this->_bodyHtml = $html;

            return $this;
        }

        // ######################################

        /**
         * @return string
         */
        public function getBodyHtml()
        {
            return $this->_bodyHtml;
        }
    }
<?php

    namespace Simplon\Email\Vo;

    class EmailConfigVo
    {
        protected $_environment;
        protected $_smptHost;
        protected $_smptPort;
        protected $_pathRootTemplates;

        // ######################################

        /**
         * @param mixed $environment
         *
         * @return EmailConfigVo
         */
        public function setEnvironment($environment)
        {
            $this->_environment = $environment;

            return $this;
        }

        // ######################################

        /**
         * @return mixed
         */
        public function getEnvironment()
        {
            return $this->_environment;
        }

        // ######################################

        /**
         * @param mixed $smptHost
         *
         * @return EmailConfigVo
         */
        public function setSmptHost($smptHost)
        {
            $this->_smptHost = $smptHost;

            return $this;
        }

        // ######################################

        /**
         * @return mixed
         * @throws \Exception
         */
        public function getSmptHost()
        {
            if (empty($this->_smptHost))
            {
                throw new \Exception(__CLASS__ . ": missing smpt host.", 500);
            }

            return $this->_smptHost;
        }

        // ######################################

        /**
         * @param mixed $smptPort
         *
         * @return EmailConfigVo
         */
        public function setSmptPort($smptPort)
        {
            $this->_smptPort = $smptPort;

            return $this;
        }

        // ######################################

        /**
         * @return mixed
         * @throws \Exception
         */
        public function getSmptPort()
        {
            if (empty($this->_smptPort))
            {
                throw new \Exception(__CLASS__ . ": missing smpt port.", 500);
            }

            return $this->_smptPort;
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
         * @return mixed
         * @throws \Exception
         */
        public function getPathRootTemplates()
        {
            if (empty($this->_pathRootTemplates))
            {
                throw new \Exception(__CLASS__ . ": missing path to root templates folder.", 500);
            }

            return $this->_pathRootTemplates;
        }
    }
<?php

    namespace Simplon\Email\Vo;

    use Simplon\Helper\Helper;

    class EmailTemplateVo extends EmailVo
    {
        /** @var string */
        protected $_pathRootTemplates;

        /** @var string */
        protected $_pathTemplatePlainFile;

        /** @var string */
        protected $_templatePlain;

        /** @var string */
        protected $_pathTemplateHtmlFile;

        /** @var string */
        protected $_templateHtml;

        /** @var string */
        protected $_pathContentPlainFile;

        /** @var string */
        protected $_pathContentHtmlFile;

        /** @var  array */
        protected $_contentVariables;

        // ######################################

        /**
         * @param $emailTemplatePath
         *
         * @return bool|string
         */
        protected function _getEmailTemplate($emailTemplatePath)
        {
            $trimedTemplatePath = Helper::pathTrim($emailTemplatePath);
            $pathComplete = "{$this->_getPathRootTemplates()}/{$trimedTemplatePath}";

            return Helper::fileRead($pathComplete);
        }

        // ######################################

        /**
         * @param $content
         * @param $vars
         *
         * @return string
         */
        protected function _renderContentVars($content, $vars)
        {
            foreach ($vars as $k => $v)
            {
                $content = Helper::stringReplace('{{' . $k . '}}', $v, $content);
            }

            return $content;
        }

        // ######################################

        /**
         * @param $baseTemplate
         * @param $contentTemplate
         *
         * @return string
         */
        protected function _mergeBaseWithContentTemplates($baseTemplate, $contentTemplate)
        {
            $vars = $this->_getContentVariables();

            // add to content variables
            $vars['content'] = $contentTemplate;
            $vars['subject'] = $this->getSubject();

            // render content into base template
            return $this->_renderContentVars($baseTemplate, $vars);
        }

        // ######################################

        /**
         * @param array $vars
         *
         * @return EmailTemplateVo
         */
        public function setContentVariables(array $vars)
        {
            $this->_contentVariables = $vars;

            return $this;
        }

        // ######################################

        /**
         * @return array
         */
        protected function _getContentVariables()
        {
            return $this->_contentVariables;
        }

        // ######################################

        /**
         * @param string $pathTemplates
         *
         * @return EmailTemplateVo
         */
        public function setPathRootTemplates($pathTemplates)
        {
            $this->_pathRootTemplates = $pathTemplates;

            return $this;
        }

        // ######################################

        /**
         * @return string
         */
        protected function _getPathRootTemplates()
        {
            return Helper::pathTrim($this->_pathRootTemplates);
        }

        // ######################################

        /**
         * @param $plainFilePath
         *
         * @return EmailTemplateVo
         */
        public function setPathTemplatePlainFile($plainFilePath)
        {
            $this->_pathTemplatePlainFile = $plainFilePath;

            return $this;
        }

        // ######################################

        /**
         * @return bool|string
         */
        protected function _getPathTemplatePlainFile()
        {
            return $this->_getEmailTemplate($this->_pathTemplatePlainFile);
        }

        // ######################################

        /**
         * @param $plainFile
         *
         * @return EmailTemplateVo
         */
        public function setPathContentPlainFile($plainFile)
        {
            $this->_pathContentPlainFile = $plainFile;

            return $this;
        }

        // ######################################

        /**
         * @return string
         */
        protected function _getPathContentPlainFile()
        {
            return $this->_pathContentPlainFile;
        }

        // ######################################

        /**
         * @return string
         */
        protected function _getContentPlainFile()
        {
            return $this->_getEmailTemplate($this->_getPathContentPlainFile());
        }

        // ######################################

        /**
         * @return null|string
         */
        public function getBodyPlain()
        {
            // render content type
            $content = $this->_renderContentVars($this->_getContentPlainFile(), $this->_getContentVariables());

            if (empty($content))
            {
                return NULL;
            }

            return $this->_mergeBaseWithContentTemplates($this->_getPathTemplatePlainFile(), $content);
        }

        // ######################################

        /**
         * @param $htmlFilePath
         *
         * @return EmailTemplateVo
         */
        public function setPathTemplateHtmlFile($htmlFilePath)
        {
            $this->_pathTemplateHtmlFile = $htmlFilePath;

            return $this;
        }

        // ######################################

        /**
         * @return bool|string
         */
        protected function _getPathTemplateHtmlFile()
        {
            return $this->_getEmailTemplate($this->_pathTemplateHtmlFile);
        }

        // ######################################

        /**
         * @param $htmlFile
         *
         * @return EmailTemplateVo
         */
        public function setPathContentHtmlFile($htmlFile)
        {
            $this->_pathContentHtmlFile = $htmlFile;

            return $this;
        }

        // ######################################

        /**
         * @return string
         */
        protected function _getPathContentHtmlFile()
        {
            return $this->_pathContentHtmlFile;
        }

        // ######################################

        /**
         * @return string
         */
        protected function _getContentHtmlFile()
        {
            return $this->_getEmailTemplate($this->_getPathContentHtmlFile());
        }

        // ######################################

        /**
         * @return null|string
         */
        public function getBodyHtml()
        {
            // render content type
            $content = $this->_renderContentVars($this->_getContentHtmlFile(), $this->_getContentVariables());

            if (empty($content))
            {
                return NULL;
            }

            return $this->_mergeBaseWithContentTemplates($this->_getPathTemplateHtmlFile(), $content);
        }
    }
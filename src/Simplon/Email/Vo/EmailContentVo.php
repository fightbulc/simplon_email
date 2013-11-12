<?php

    namespace Simplon\Email\Vo;

    use Simplon\Helper\Helper;

    class EmailContentVo
    {
        protected $_fileNameBaseTemplate = 'base';
        protected $_fileNameContentTemplate = 'content';

        /** @var String */
        protected $_pathBaseTemplates;

        /** @var String */
        protected $_pathContentTemplates;

        /** @var Array */
        protected $_contentVariables = [];

        /** @var Array */
        protected $_embeddedImages = [];

        // ######################################

        /**
         * @return string
         */
        protected function _getFileNameBaseTemplate()
        {
            return $this->_fileNameBaseTemplate;
        }

        // ######################################

        /**
         * @return string
         */
        protected function _getFileNameContentTemplate()
        {
            return $this->_fileNameContentTemplate;
        }

        // ######################################

        /**
         * @param $typeTemplate
         * @param string $typeContent
         *
         * @return string
         */
        protected function _buildPathBaseFile($typeTemplate, $typeContent = 'plain')
        {
            return "{$this->_getPathBaseTemplates()}/{$typeTemplate}.{$typeContent}";
        }

        // ######################################

        /**
         * @param $typeTemplate
         * @param string $typeContent
         *
         * @return string
         */
        protected function _buildPathContentFile($typeTemplate, $typeContent = 'plain')
        {
            return "{$this->_getPathContentTemplates()}/{$typeTemplate}.{$typeContent}";
        }

        // ######################################

        /**
         * @param $pathFileTemplate
         *
         * @return bool|string
         */
        protected function _fetchTemplateFile($pathFileTemplate)
        {
            return Helper::fileRead($pathFileTemplate);
        }

        // ######################################

        /**
         * @param $content
         *
         * @return mixed
         */
        protected function _translateLanguageStrings($content)
        {
            return preg_replace_callback('/\'\'\'(.+?)\'\'\'/sum', function ($matches) {
                return $matches[1];
            }, $content);
        }

        // ######################################

        /**
         * @param $content
         * @param array $vars
         *
         * @return mixed
         */
        public function renderContentVariables($content, array $vars)
        {
            $content = $this->_translateLanguageStrings($content);

            foreach ($vars as $k => $v)
            {
                // handle loops over arrays
                if (is_array($v)){
                    $that = $this;
                    $content = preg_replace_callback('/{{#' . $k . '}}(.*?){{\/' . $k . '}}/sum', function ($matches) use ($v, $that) {

                        $content = '';

                        foreach ($v as $item)
                        {
                            // this is why the method has to be public :)
                            $content .= $that->renderContentVariables($matches[1], $item);
                        }

                        return $content;

                    }, $content);
                }

                // handle string values
                else
                {
                    $content = Helper::stringReplace('{{' . $k . '}}', $v, $content);
                }
            }

            return $content;
        }

        // ######################################

        /**
         * @param $contentBody
         *
         * @return array|bool
         */
        protected function _findContentImages($contentBody)
        {
            preg_match_all('/{{image:(.*?)}}/u', $contentBody, $matches);

            if (isset($matches[1]))
            {
                return $matches[1];
            }

            return FALSE;
        }

        // ######################################

        /**
         * @param $swiftEncodedImageSource
         *
         * @return int
         */
        protected function _addEmbeddedImage($swiftEncodedImageSource)
        {
            $this->_embeddedImages[] = $swiftEncodedImageSource;

            return count($this->_embeddedImages) - 1;
        }

        // ######################################

        /**
         * @return array|null
         */
        public function getEmbeddedImages()
        {
            if (empty($this->_embeddedImages))
            {
                return NULL;
            }

            return $this->_embeddedImages;
        }

        // ######################################

        /**
         * @param $nameImage
         *
         * @return string
         */
        protected function _getPathContentImage($nameImage)
        {
            $nameImage = Helper::urlTrim($nameImage);

            return "{$this->_getPathContentTemplates()}/{$nameImage}";
        }

        // ######################################

        /**
         * @param $contentBody
         *
         * @return mixed
         */
        protected function _prepareContentImages($contentBody)
        {
            $contentImages = $this->_findContentImages($contentBody);

            if ($contentImages !== FALSE)
            {
                foreach ($contentImages as $nameImage)
                {
                    $pathImage = $this->_getPathContentImage($nameImage);
                    $stackIndex = $this->_addEmbeddedImage($pathImage);
                    $contentBody = str_replace('{{image:' . $nameImage . '}}', '{{imageStackIndex:' . $stackIndex . '}}', $contentBody);
                }
            }

            return $contentBody;
        }

        // ######################################

        /**
         * @param mixed $pathTemplates
         *
         * @return EmailContentVo
         */
        public function setPathBaseTemplates($pathTemplates)
        {
            $this->_pathBaseTemplates = $pathTemplates;

            return $this;
        }

        // ######################################

        /**
         * @return string
         */
        protected function _getPathBaseTemplates()
        {
            return Helper::pathTrim($this->_pathBaseTemplates);
        }

        // ######################################

        /**
         * @return bool
         */
        protected function _hasPathBaseTemplates()
        {
            return empty($this->_pathBaseTemplates) ? FALSE : TRUE;
        }

        // ######################################

        /**
         * @param mixed $pathTemplates
         *
         * @return EmailContentVo
         */
        public function setPathContentTemplates($pathTemplates)
        {
            $this->_pathContentTemplates = $pathTemplates;

            return $this;
        }

        // ######################################

        /**
         * @return string
         */
        protected function _getPathContentTemplates()
        {
            return Helper::pathTrim($this->_pathContentTemplates);
        }

        // ######################################

        /**
         * @param mixed $contentVariables
         *
         * @return EmailContentVo
         */
        public function setContentVariables($contentVariables)
        {
            $this->_contentVariables = $contentVariables;

            return $this;
        }

        // ######################################

        /**
         * @return Array
         */
        protected function _getContentVariables()
        {
            return $this->_contentVariables;
        }

        // ######################################

        /**
         * @return string
         */
        protected function _getPathBasePlainFile()
        {
            return $this->_buildPathBaseFile($this->_getFileNameBaseTemplate(), 'plain');
        }

        // ######################################

        /**
         * @return bool|string
         */
        protected function _getFileBasePlain()
        {
            if ($this->_hasPathBaseTemplates())
            {
                $basePlain = $this->_fetchTemplateFile($this->_getPathBasePlainFile());

                if ($basePlain !== FALSE)
                {
                    return $basePlain;
                }
            }

            return FALSE;
        }

        // ######################################

        /**
         * @return bool
         */
        protected function _hasBasePlain()
        {
            return $this->_getFileBasePlain() !== FALSE ? TRUE : FALSE;
        }

        // ######################################

        /**
         * @return string
         */
        protected function _getPathContentPlainFile()
        {
            return $this->_buildPathContentFile($this->_getFileNameContentTemplate(), 'plain');
        }

        // ######################################

        /**
         * @return bool|string
         */
        protected function _getFileContentPlain()
        {
            $contentPlain = $this->_fetchTemplateFile($this->_getPathContentPlainFile());

            if ($contentPlain !== FALSE)
            {
                return $contentPlain;
            }

            return FALSE;
        }

        // ######################################

        /**
         * @return bool
         */
        public function hasContentPlain()
        {
            return $this->_getFileContentPlain() !== FALSE ? TRUE : FALSE;
        }

        // ######################################

        /**
         * @return mixed|null
         */
        public function getBodyPlain()
        {
            if ($this->hasContentPlain())
            {
                // content
                $contentPlain = $this->renderContentVariables($this->_getFileContentPlain(), $this->_getContentVariables());

                // base
                if ($this->_hasBasePlain())
                {
                    $contentVariables = $this->_getContentVariables();
                    $contentVariables['content'] = $contentPlain;
                    $contentPlain = $this->renderContentVariables($this->_getFileBasePlain(), $contentVariables);
                }

                return $contentPlain;
            }

            return NULL;
        }

        // ######################################

        /**
         * @return string
         */
        protected function _getPathBaseHtmlFile()
        {
            return $this->_buildPathBaseFile($this->_getFileNameBaseTemplate(), 'html');
        }

        // ######################################

        /**
         * @return bool|string
         */
        protected function _getFileBaseHtml()
        {
            if ($this->_hasPathBaseTemplates())
            {
                $baseHtml = $this->_fetchTemplateFile($this->_getPathBaseHtmlFile());

                if ($baseHtml !== FALSE)
                {
                    return $baseHtml;
                }
            }

            return FALSE;
        }

        // ######################################

        /**
         * @return bool
         */
        protected function _hasBaseHtml()
        {
            return $this->_getFileBaseHtml() !== FALSE ? TRUE : FALSE;
        }

        // ######################################

        /**
         * @return string
         */
        protected function _getPathContentHtmlFile()
        {
            return $this->_buildPathContentFile($this->_getFileNameContentTemplate(), 'html');
        }

        // ######################################

        /**
         * @return bool|string
         */
        protected function _getFileContentHtml()
        {
            $contentHtml = $this->_fetchTemplateFile($this->_getPathContentHtmlFile());

            if ($contentHtml !== FALSE)
            {
                return $contentHtml;
            }

            return FALSE;
        }

        // ######################################

        /**
         * @return bool
         */
        public function hasContentHtml()
        {
            return $this->_getFileContentHtml() !== FALSE ? TRUE : FALSE;
        }

        // ######################################

        /**
         * @return mixed|null
         */
        public function getBodyHtml()
        {
            if ($this->hasContentHtml())
            {
                // content
                $contentHtml = $this->renderContentVariables($this->_getFileContentHtml(), $this->_getContentVariables());

                // base
                if ($this->_hasBaseHtml())
                {
                    $contentVariables = $this->_getContentVariables();
                    $contentVariables['content'] = $contentHtml;
                    $contentHtml = $this->renderContentVariables($this->_getFileBaseHtml(), $contentVariables);
                }

                // encode images
                $contentHtml = $this->_prepareContentImages($contentHtml);

                return $contentHtml;
            }

            return NULL;
        }
    }
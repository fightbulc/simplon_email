<?php

namespace Simplon\Email\Vo;

use Simplon\Helper\Helper;

/**
 * EmailContentVo
 * @package Simplon\Email\Vo
 * @author  Tino Ehrich (tino@bigpun.me)
 */
class EmailContentVo
{
    /**
     * @var string
     */
    protected $fileNameBaseTemplate = 'base';

    /**
     * @var string
     */
    protected $fileNameContentTemplate = 'content';

    /**
     * @var string
     */
    protected $pathBaseTemplates;

    /**
     * @var string
     */
    protected $pathContentTemplates;

    /**
     * @var array
     */
    protected $contentVariables = [];

    /**
     * @var array
     */
    protected $localeStrings = [];

    /**
     * @var array
     */
    protected $embeddedImages = [];

    /**
     * @param       $content
     * @param array $vars
     *
     * @return mixed
     */
    public function renderContentVariables($content, array $vars)
    {
        $content = $this->translateLocaleStrings($content);

        foreach ($vars as $k => $v)
        {
            // handle loops over arrays
            if (is_array($v))
            {
                $that = $this;

                $content = preg_replace_callback(
                    '/{{#' . $k . '}}(.*?){{\/' . $k . '}}/sum',
                    function ($matches) use ($v, $that)
                    {
                        $content = '';

                        foreach ($v as $item)
                        {
                            // this is why the method has to be public :)
                            $content .= $that->renderContentVariables($matches[1], $item);
                        }

                        return $content;

                    },
                    $content
                );
            }

            // handle string values
            else
            {
                $content = preg_replace('/\{\{' . $k . '\}\}/u', $v, $content);
            }
        }

        return $content;
    }

    /**
     * @return array
     */
    public function getEmbeddedImages()
    {
        if (empty($this->embeddedImages))
        {
            return [];
        }

        return $this->embeddedImages;
    }

    /**
     * @param mixed $pathTemplates
     *
     * @return EmailContentVo
     */
    public function setPathBaseTemplates($pathTemplates)
    {
        $this->pathBaseTemplates = $pathTemplates;

        return $this;
    }

    /**
     * @param mixed $pathTemplates
     *
     * @return EmailContentVo
     */
    public function setPathContentTemplates($pathTemplates)
    {
        $this->pathContentTemplates = $pathTemplates;

        return $this;
    }

    /**
     * @param mixed $contentVariables
     *
     * @return EmailContentVo
     */
    public function setContentVariables($contentVariables)
    {
        $this->contentVariables = $contentVariables;

        return $this;
    }

    /**
     * @param $localeStrings
     *
     * @return $this
     */
    public function setLocaleStrings($localeStrings)
    {
        $this->localeStrings = $localeStrings;

        return $this;
    }

    /**
     * @param string $string
     *
     * @return string
     */
    public function getTranslation($string)
    {
        if (isset($this->localeStrings[$string]))
        {
            return $this->localeStrings[$string];
        }

        return $string;
    }

    /**
     * @return bool
     */
    public function hasContentPlain()
    {
        return $this->getFileContentPlain() !== false ? true : false;
    }

    /**
     * @return mixed|null
     */
    public function getBodyPlain()
    {
        if ($this->hasContentPlain())
        {
            // content
            $contentPlain = $this->renderContentVariables($this->getFileContentPlain(), $this->getContentVariables());

            // base
            if ($this->hasBasePlain())
            {
                $contentVariables = $this->getContentVariables();
                $contentVariables['content'] = $contentPlain;
                $contentPlain = $this->renderContentVariables($this->getFileBasePlain(), $contentVariables);
            }

            return $contentPlain;
        }

        return null;
    }

    /**
     * @return bool
     */
    public function hasContentHtml()
    {
        return $this->getFileContentHtml() !== false ? true : false;
    }

    /**
     * @return mixed|null
     */
    public function getBodyHtml()
    {
        if ($this->hasContentHtml())
        {
            // content
            $contentHtml = $this->renderContentVariables($this->getFileContentHtml(), $this->getContentVariables());

            // base
            if ($this->hasBaseHtml())
            {
                $contentVariables = $this->getContentVariables();
                $contentVariables['content'] = $contentHtml;
                $contentHtml = $this->renderContentVariables($this->getFileBaseHtml(), $contentVariables);
            }

            // encode images
            $contentHtml = $this->prepareContentImages($contentHtml);

            return $contentHtml;
        }

        return null;
    }

    /**
     * @return string
     */
    protected function getFileNameBaseTemplate()
    {
        return $this->fileNameBaseTemplate;
    }

    /**
     * @return string
     */
    protected function getFileNameContentTemplate()
    {
        return $this->fileNameContentTemplate;
    }

    /**
     * @param string $typeTemplate
     * @param string $typeContent
     *
     * @return string
     */
    protected function buildPathBaseFile($typeTemplate, $typeContent = 'plain')
    {
        return "{$this->getPathBaseTemplates()}/{$typeTemplate}.{$typeContent}";
    }

    /**
     * @param        $typeTemplate
     * @param string $typeContent
     *
     * @return string
     */
    protected function buildPathContentFile($typeTemplate, $typeContent = 'plain')
    {
        return "{$this->getPathContentTemplates()}/{$typeTemplate}.{$typeContent}";
    }

    /**
     * @param $pathFileTemplate
     *
     * @return bool|string
     */
    protected function fetchTemplateFile($pathFileTemplate)
    {
        return Helper::fileGetContent($pathFileTemplate);
    }

    /**
     * @param $content
     *
     * @return mixed
     */
    protected function translateLocaleStrings($content)
    {
        $locale = $this->getLocaleStrings();

        return preg_replace_callback('/\'\'\'(.+?)\'\'\'/sum', function ($matches) use ($locale)
        {
            $key = $matches[1];

            if (isset($locale[$key]))
            {
                return $locale[$key];
            }

            return $key;
        }, $content);
    }

    /**
     * @param $contentBody
     *
     * @return array|bool
     */
    protected function findContentImages($contentBody)
    {
        preg_match_all('/{{image:(.*?)}}/u', $contentBody, $matches);

        if (isset($matches[1]))
        {
            return $matches[1];
        }

        return false;
    }

    /**
     * @param $swiftEncodedImageSource
     *
     * @return int
     */
    protected function addEmbeddedImage($swiftEncodedImageSource)
    {
        $this->embeddedImages[] = $swiftEncodedImageSource;

        return count($this->embeddedImages) - 1;
    }

    /**
     * @param $nameImage
     *
     * @return string
     */
    protected function getPathContentImage($nameImage)
    {
        $nameImage = Helper::urlTrim($nameImage);

        return "{$this->getPathContentTemplates()}/{$nameImage}";
    }

    /**
     * @param $contentBody
     *
     * @return mixed
     */
    protected function prepareContentImages($contentBody)
    {
        $contentImages = $this->findContentImages($contentBody);

        if ($contentImages !== false)
        {
            foreach ($contentImages as $nameImage)
            {
                $pathImage = $this->getPathContentImage($nameImage);
                $stackIndex = $this->addEmbeddedImage($pathImage);
                $contentBody = str_replace('{{image:' . $nameImage . '}}', '{{imageStackIndex:' . $stackIndex . '}}', $contentBody);
            }
        }

        return $contentBody;
    }

    /**
     * @return string
     */
    protected function getPathBaseTemplates()
    {
        return Helper::pathTrim($this->pathBaseTemplates);
    }

    /**
     * @return bool
     */
    protected function hasPathBaseTemplates()
    {
        return empty($this->pathBaseTemplates) ? false : true;
    }

    /**
     * @return string
     */
    protected function getPathContentTemplates()
    {
        return Helper::pathTrim($this->pathContentTemplates);
    }

    /**
     * @return Array
     */
    protected function getContentVariables()
    {
        return $this->contentVariables;
    }

    /**
     * @return Array
     */
    protected function getLocaleStrings()
    {
        return $this->localeStrings;
    }

    /**
     * @return string
     */
    protected function getPathBasePlainFile()
    {
        return $this->buildPathBaseFile($this->getFileNameBaseTemplate(), 'plain');
    }

    /**
     * @return bool|string
     */
    protected function getFileBasePlain()
    {
        if ($this->hasPathBaseTemplates())
        {
            $basePlain = $this->fetchTemplateFile($this->getPathBasePlainFile());

            if ($basePlain !== false)
            {
                return $basePlain;
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    protected function hasBasePlain()
    {
        return $this->getFileBasePlain() !== false ? true : false;
    }

    /**
     * @return string
     */
    protected function getPathContentPlainFile()
    {
        return $this->buildPathContentFile($this->getFileNameContentTemplate(), 'plain');
    }

    /**
     * @return bool|string
     */
    protected function getFileContentPlain()
    {
        $contentPlain = $this->fetchTemplateFile($this->getPathContentPlainFile());

        if ($contentPlain !== false)
        {
            return $contentPlain;
        }

        return false;
    }

    /**
     * @return string
     */
    protected function getPathBaseHtmlFile()
    {
        return $this->buildPathBaseFile($this->getFileNameBaseTemplate(), 'html');
    }

    /**
     * @return bool|string
     */
    protected function getFileBaseHtml()
    {
        if ($this->hasPathBaseTemplates())
        {
            $baseHtml = $this->fetchTemplateFile($this->getPathBaseHtmlFile());

            if ($baseHtml !== false)
            {
                return $baseHtml;
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    protected function hasBaseHtml()
    {
        return $this->getFileBaseHtml() !== false ? true : false;
    }

    /**
     * @return string
     */
    protected function getPathContentHtmlFile()
    {
        return $this->buildPathContentFile($this->getFileNameContentTemplate(), 'html');
    }

    /**
     * @return bool|string
     */
    protected function getFileContentHtml()
    {
        $contentHtml = $this->fetchTemplateFile($this->getPathContentHtmlFile());

        if ($contentHtml !== false)
        {
            return $contentHtml;
        }

        return false;
    }
}
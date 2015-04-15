<?php

namespace Simplon\Email\Vo;

use Simplon\Helper\Helper;
use Simplon\Mustache\Mustache;

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
    private $fileNameBaseTemplate = 'base';

    /**
     * @var string
     */
    private $fileNameContentTemplate = 'content';

    /**
     * @var string
     */
    private $pathBaseTemplates;

    /**
     * @var string
     */
    private $pathContentTemplates;

    /**
     * @var array
     */
    private $contentParams = [];

    /**
     * @var array
     */
    private $embeddedImages = [];

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
     * @param string $pathTemplates
     *
     * @return EmailContentVo
     */
    public function setPathBaseTemplates($pathTemplates)
    {
        $this->pathBaseTemplates = $pathTemplates;

        return $this;
    }

    /**
     * @param string $pathTemplates
     *
     * @return EmailContentVo
     */
    public function setPathContentTemplates($pathTemplates)
    {
        $this->pathContentTemplates = $pathTemplates;

        return $this;
    }

    /**
     * @return array
     */
    public function getContentParams()
    {
        return $this->contentParams;
    }

    /**
     * @param array $contentParams
     *
     * @return EmailContentVo
     */
    public function setContentParams(array $contentParams)
    {
        $this->contentParams = $contentParams;

        return $this;
    }

    /**
     * @param string $template
     * @param array  $params
     *
     * @return string
     */
    public function renderContentParams($template, array $params = [])
    {
        if (empty($params) === true)
        {
            $params = $this->getContentParams();
        }

        return Mustache::render($template, $params);
    }

    /**
     * @return bool
     */
    public function hasContentPlain()
    {
        return $this->getFileContentPlain() !== false ? true : false;
    }

    /**
     * @return string|null
     */
    public function getBodyPlain()
    {
        if ($this->hasContentPlain())
        {
            // content
            $contentPlain = $this->renderContentParams($this->getFileContentPlain());

            // base
            if ($this->hasBasePlain())
            {
                $contentParams = $this->getContentParams();
                $contentParams['content'] = $contentPlain;
                $contentPlain = $this->renderContentParams($this->getFileBasePlain(), $contentParams);
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
     * @return string|null
     */
    public function getBodyHtml()
    {
        if ($this->hasContentHtml())
        {
            // content
            $contentHtml = $this->renderContentParams($this->getFileContentHtml());

            // base
            if ($this->hasBaseHtml())
            {
                $contentParams = $this->getContentParams();
                $contentParams['content'] = $contentHtml;
                $contentHtml = $this->renderContentParams($this->getFileBaseHtml(), $contentParams);
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
     * @param string $contentBody
     *
     * @return string
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
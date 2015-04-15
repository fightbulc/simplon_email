<?php

namespace Simplon\Email\Vo;

use Simplon\Helper\Helper;
use Simplon\Mustache\Mustache;

/**
 * EmailVo
 * @package Simplon\Email\Vo
 * @author  Tino Ehrich (tino@bigpun.me)
 */
class EmailVo
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
     * @var string
     */
    private $fromAddress;

    /**
     * @var string
     */
    private $fromName;

    /**
     * @var string
     */
    private $toAddress;

    /**
     * @var string
     */
    private $toName;

    /**
     * @var array
     */
    private $ccAddresses;

    /**
     * @var array
     */
    private $bccAddresses;

    /**
     * @var string
     */
    private $subject;

    /**
     * @var array
     */
    private $contentData = [];

    /**
     * @var array
     */
    private $embeddedImages = [];

    /**
     * @var \Swift_Attachment[]
     */
    private $attachments = [];

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
     * @return EmailVo
     */
    public function setPathBaseTemplates($pathTemplates)
    {
        $this->pathBaseTemplates = $pathTemplates;

        return $this;
    }

    /**
     * @param string $pathTemplates
     *
     * @return EmailVo
     */
    public function setPathContentTemplates($pathTemplates)
    {
        $this->pathContentTemplates = $pathTemplates;

        return $this;
    }

    /**
     * @param array $contentData
     *
     * @return EmailVo
     */
    public function setContentData(array $contentData)
    {
        $this->contentData = $contentData;

        return $this;
    }

    /**
     * @return bool
     */
    protected function hasContentPlain()
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
            $contentPlain = $this->renderParams(
                $this->getFileContentPlain(),
                $this->getContentData()
            );

            // base
            if ($this->hasBasePlain())
            {
                $contentParams = $this->getContentData();
                $contentParams['content'] = $contentPlain;
                $contentPlain = $this->renderParams(
                    $this->getFileBasePlain(),
                    $contentParams
                );
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
            $contentHtml = $this->renderParams(
                $this->getFileContentHtml(),
                $this->getContentData()
            );

            // base
            if ($this->hasBaseHtml())
            {
                $contentParams = $this->getContentData();
                $contentParams['content'] = $contentHtml;
                $contentHtml = $this->renderParams(
                    $this->getFileBaseHtml(),
                    $contentParams
                );
            }

            // encode images
            $contentHtml = $this->prepareContentImages($contentHtml);

            return $contentHtml;
        }

        return null;
    }

    /**
     * @param $address
     * @param $name
     *
     * @return EmailVo
     */
    public function setFrom($address, $name = null)
    {
        $this->fromAddress = $address;
        $this->fromName = $name;

        return $this;
    }

    /**
     * @return array|string
     */
    public function getFrom()
    {
        if (empty($this->fromName))
        {
            return $this->fromAddress;
        }

        return [$this->fromAddress => $this->fromName];
    }

    /**
     * @param $address
     * @param $name
     *
     * @return EmailVo
     */
    public function setTo($address, $name = null)
    {
        $this->toAddress = $address;
        $this->toName = $name;

        return $this;
    }

    /**
     * @return array|string
     */
    public function getTo()
    {
        if (empty($this->toName))
        {
            return $this->toAddress;
        }

        return [$this->toAddress => $this->toName];
    }

    /**
     * @param array $addresses
     *
     * @return EmailVo
     */
    public function setCc(array $addresses)
    {
        $this->ccAddresses = $addresses;

        return $this;
    }

    /**
     * @return array|null
     */
    public function getCc()
    {
        if (empty($this->ccAddresses))
        {
            return null;
        }

        return $this->ccAddresses;
    }

    /**
     * @param array $addresses
     *
     * @return EmailVo
     */
    public function setBcc(array $addresses)
    {
        $this->bccAddresses = $addresses;

        return $this;
    }

    /**
     * @return array|null
     */
    public function getBcc()
    {
        if (empty($this->bccAddresses))
        {
            return null;
        }

        return $this->bccAddresses;
    }

    /**
     * @param $value
     *
     * @return EmailVo
     */
    public function setSubject($value)
    {
        $this->subject = $value;

        return $this;
    }

    /**
     * @return string
     */
    public function getSubject()
    {
        return $this->renderParams(
            $this->subject,
            $this->getContentData()
        );
    }

    /**
     * @param string      $data
     * @param string      $fileName
     * @param string|null $contentType
     *
     * @return EmailVo
     */
    public function addAttachment($data, $fileName, $contentType = null)
    {
        $this->attachments[] = \Swift_Attachment::newInstance($data, $fileName, $contentType);

        return $this;
    }

    /**
     * @return bool
     */
    public function hasAttachments()
    {
        return count($this->attachments) !== 0;
    }

    /**
     * @return \Swift_Attachment[]
     */
    public function getAttachments()
    {
        return $this->attachments;
    }

    /**
     * @return array
     */
    protected function getContentData()
    {
        return $this->contentData;
    }

    /**
     * @param string $template
     * @param array  $params
     *
     * @return string
     */
    protected function renderParams($template, array $params)
    {
        return Mustache::render($template, $params);
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
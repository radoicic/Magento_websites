<?php

namespace Meetanshi\Callforprice\Model\Config\Source;

use Magento\Config\Model\Config\CommentInterface;
use Magento\Framework\View\Element\AbstractBlock;

/**
 * Class RecaptchSecretKeyComment
 */
class RecaptchSecretKeyComment extends AbstractBlock implements CommentInterface
{
    /**
     * @param string $elementValue
     * @return string
     */
    public function getCommentText($elementValue)
    {
        return "Register with <a target=\"_blank\" href=\"https://www.google.com/recaptcha/admin\">Google reCAPTCHA </a>to get your secret key.";
    }
}

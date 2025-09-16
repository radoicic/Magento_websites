<?php

namespace Meetanshi\Callforprice\Model\Config\Source;

use Magento\Config\Model\Config\CommentInterface;
use Magento\Framework\View\Element\AbstractBlock;

/**
 * Class Msg91Comment
 */
class Msg91Comment extends AbstractBlock implements CommentInterface
{
    /**
     * @param string $elementValue
     * @return string
     */
    public function getCommentText($elementValue)
    {
        return "Msg91 URL is http://api.msg91.com/api/sendhttp.php. <br> Text Local URL is https://api.textlocal.in/send/.";
    }
}

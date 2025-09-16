<?php
namespace TiDesign\Videobackground\Model;

use TiDesign\Videobackground\Api\Data\VideolistInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;

class Videolist extends AbstractModel implements VideolistInterface, IdentityInterface
{
 
    protected function _construct()
    {
        $this->_init('TiDesign\Videobackground\Model\ResourceModel\Videolist');
    }
    public function getIdentities()
    {
        return [$this->getId()];
    } 
 
 	public function getId()
	{
		return parent::getData(self::SAM_ID);
	}
	
 	public function getActive()
	{
		return parent::getData(self::ACTIVE);
	}	
	
 	public function getStoreId()
	{
		return parent::getData(self::STR_ID);
	}
	
 	public function getCategoryId()
	{
		return parent::getData(self::CAT_ID);
	}
		
 	public function getCreateDate()
	{
		return parent::getData(self::C_DATE);
	}
	
 	public function getVideoUrl()
	{
		return parent::getData(self::VIDEO);
	}
	
 	public function getVideoMute()
	{
		return parent::getData(self::VID_MU);
	}
	
 	public function getVideoLoop()
	{
		return parent::getData(self::VID_LO);
	}
	
 	public function getShowOnEntire()
	{
		return parent::getData(self::SHOW_E);
	}
	
 	public function getShowOnCategoryProducts()
	{
		return parent::getData(self::CAT_PR);
	}
	
 	public function getStaticPages()
	{
		return parent::getData(self::STA_PAG);
	}
	
 	public function getPageId()
	{
		return parent::getData(self::PAG_ID);
	}
	
 	public function getVideoSource()
	{
		return parent::getData(self::VID_SO);
	}
	
 	public function getLocalWebm()
	{
		return parent::getData(self::LOCALW);
	}
	
 	public function getLocalMp4()
	{
		return parent::getData(self::LOCALM);
	}
	





	
	public function setId($id)
	{
		return $this->setData(self::SAM_ID, $id);
	}
		
	public function setActive($active)
	{
		return $this->setData(self::ACTIVE, $active);
	}
	
	public function setStoreId($store_id)
	{
		return $this->setData(self::STR_ID, $store_id);
	}
	
	public function setCategoryId($category_id)
	{
		return $this->setData(self::CAT_ID, $category_id);
	}
	
	public function setCreateDate($create_date)
	{
		return $this->setData(self::C_DATE, $create_date);
	}
	
 	public function setVideoUrl($video_url)
	{
		return $this->setData(self::VIDEO, $video_url);
	}
	
 	public function setVideoMute($video_mute)
	{
		return $this->setData(self::VID_MU, $video_mute);
	}
	
 	public function setVideoLoop($video_loop)
	{
		return $this->setData(self::VID_LO, $video_loop);
	}
	
 	public function setShowOnEntire($show_on_entire)
	{
		return parent::setData(self::SHOW_E, $show_on_entire);
	}
	
 	public function setShowOnCategoryProducts($show_on_category_products)
	{
		return parent::setData(self::CAT_PR, $show_on_category_products);
	}
	
 	public function setStaticPages($static_pages)
	{
		return parent::setData(self::STA_PAG, $static_pages);
	}
	
 	public function setPageId($page_id)
	{
		return parent::setData(self::PAG_ID, $page_id);
	}	
 	public function setVideoSource($video_source)
	{
		return parent::setData(self::VID_SO, $video_source);
	}	
 	public function setLocalWebm($local_webm)
	{
		return parent::setData(self::LOCALW, $local_webm);
	}	
 	public function setLocalMp4($local_mp4)
	{
		return parent::setData(self::LOCALM, $local_mp4);
	}
	
	
}
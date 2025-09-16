<?php

namespace TiDesign\Videobackground\Api\Data;

interface VideolistInterface
{
	const SAM_ID			= 'id';
	const ACTIVE			= 'active';
	const STR_ID			= 'store_id';
	const CAT_ID			= 'category_id';
	const CAT_PR			= 'show_on_category_products';
	const C_DATE			= 'created_date';
	const VIDEO				= 'video_url';
	const VID_MU			= 'video_mute';
	const VID_LO			= 'video_loop';
	const SHOW_E			= 'show_on_entire';
	const STA_PAG			= 'static_pages';
	const PAG_ID			= 'page_id';
	
	const VID_SO			= 'video_source';
	const LOCALW			= 'local_webm';
	const LOCALM			= 'local_mp4';
	
	
	public function getId();
	
	public function getActive();
	
	public function getStoreId();
	
	public function getCategoryId();
	
	public function getCreateDate();
	
	public function getVideoUrl();
	
	public function getVideoMute();
	
	public function getVideoLoop();
	
	public function getShowOnEntire();
	
	public function getShowOnCategoryProducts();
	
	public function getStaticPages();
	
	public function getPageId();
	
	public function getVideoSource();
	
	public function getLocalWebm();
	
	public function getLocalMp4();


	public function setId($id);
	
	public function setActive($active);
	
	public function setStoreId($store_id);
	
	public function setCategoryId($category_id);
	
	public function setCreateDate($create_date);
	
	public function setVideoUrl($video_url);
	
	public function setVideoMute($video_mute);
	
	public function setVideoLoop($video_loop);
	
	public function setShowOnEntire($show_on_entire);
	
	public function setShowOnCategoryProducts($show_on_category_products);
	
	public function setStaticPages($static_pages);
	
	public function setPageId($page_id);
	
	public function setVideoSource($video_source);
	
	public function setLocalWebm($local_webm);
	
	public function setLocalMp4($local_mp4);
	
}	
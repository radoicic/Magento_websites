<?php
namespace TiDesign\CacheCreator\Block;

use Magento\Catalog\Model\Category as ModelCategory;

class Topmenu extends \Smartwave\Megamenu\Block\Topmenu
{

	private		$_emulation;
    protected 	$_categoryHelper;
    protected 	$_categoryFlatConfig;
    protected 	$_topMenu;
    protected 	$_categoryFactory;
    protected 	$_helper;
    protected 	$_filterProvider;
    protected 	$_blockFactory;
    protected 	$_megamenuConfig;
    protected 	$_storeManager;
	protected 	$_categoryCollection;
	protected 	$_storeId;
	protected 	$_logger;
	protected 	$_dataCollectionFactory;
	protected 	$_scopeConfig;
	protected 	$_categoryRepository;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Catalog\Helper\Category $categoryHelper,
        \Smartwave\Megamenu\Helper\Data $helper,
        \Magento\Catalog\Model\Indexer\Category\Flat\State $categoryFlatState,
		\Magento\Catalog\Model\CategoryRepository $categoryRepository,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
		\Magento\Framework\Data\CollectionFactory $dataCollectionFactory,
        \Magento\Theme\Block\Html\Topmenu $topMenu,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        \Magento\Cms\Model\BlockFactory $blockFactory,
		\Magento\Store\Model\App\Emulation $Emulation,
		\Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollection,
		\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
		\TiDesign\Consignment\Logger\Logger $customLogger
    ) {
        $this->_categoryHelper = $categoryHelper;
        $this->_categoryFlatConfig = $categoryFlatState;
        $this->_categoryFactory = $categoryFactory;
		$this->_dataCollectionFactory = $dataCollectionFactory;
        $this->_topMenu = $topMenu;
        $this->_helper = $helper;
        $this->_filterProvider = $filterProvider;
        $this->_blockFactory = $blockFactory;
		$this->_emulation	= $Emulation;
		$this->_categoryCollection = $categoryCollection;
		$this->_scopeConfig = $scopeConfig;
		$this->_categoryRepository = $categoryRepository;
		$this->_logger		= $customLogger;
        $this->_storeManager = $context->getStoreManager();

        parent::__construct($context, $categoryHelper, $helper, $categoryFlatState, $categoryFactory, $topMenu, $filterProvider, $blockFactory);
    }
	
	public function getFrontendMenu($storeId){
		$this->_storeId = $storeId;
		$this->_emulation->startEnvironmentEmulation($storeId, \Magento\Framework\App\Area::AREA_FRONTEND, true);
		$menu = $this->getMegamenuHtml();
		$this->_emulation->stopEnvironmentEmulation();
		return $menu;
	}
	
    /**
     * Retrieve current store categories
     *
     * @param bool|string $sorted
     * @param bool $asCollection
     * @param bool $toLoad
     * @return \Magento\Framework\Data\Tree\Node\Collection or
     * \Magento\Catalog\Model\ResourceModel\Category\Collection or array
     */
    public function getStoreCategories($sorted = false, $asCollection = false, $toLoad = true)
    {
        $parent = $this->_storeManager->getStore($this->_storeId)->getRootCategoryId();
        $cacheKey = sprintf('%d-%d-%d-%d', $parent, $sorted, $asCollection, $toLoad);
        if (isset($this->_storeCategories[$cacheKey])) {
            return $this->_storeCategories[$cacheKey];
        }

        $category = $this->_categoryFactory->create()->setStoreId($this->_storeId);
        /* @var $category ModelCategory */
        if (!$category->checkId($parent)) {
            if ($asCollection) {
                return $this->_dataCollectionFactory->create();
            }
            return [];
        }

        $recursionLevel = max(
            0,
            (int)$this->_scopeConfig->getValue(
                'catalog/navigation/max_depth',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            )
        );
        $storeCategories = $category->getCategories($parent, $recursionLevel, $sorted, $asCollection, $toLoad);
        $this->_storeCategories[$cacheKey] = $storeCategories;
        return $storeCategories;
    }
    /**
     * Retrieve category url
     *
     * @param ModelCategory $category
     * @return string
     */
    public function getCategoryUrl($category)
    {
						
		$cat = $this->_categoryRepository->get($category->getId(), $this->_storeId);
		$cat->getUrlInstance()->setScope($this->_storeId);
		return $cat->getUrl();					
							
    }	
	
    public function getSubmenuItemsHtml($children, $level = 1, $max_level = 0, $column_width=12, $menu_type = 'fullwidth', $columns = null)
    {
        $html = '';

        if(!$max_level || ($max_level && $max_level == 0) || ($max_level && $max_level > 0 && $max_level-1 >= $level)) {
            $column_class = "";
            if($level == 1 && $columns && ($menu_type == 'fullwidth' || $menu_type == 'staticwidth')) {
                $column_class = "col-md-".$column_width." ";
                $column_class .= "mega-columns columns".$columns;
            }
            $html = '<ul class="subchildmenu '.$column_class.'">';
            foreach($children as $child) {
                $cat_model = $this->getCategoryModel($child->getId());

                $sw_menu_hide_item = $cat_model->getData('sw_menu_hide_item');

                if (!$sw_menu_hide_item) {
                    $sub_children = $this->getActiveChildCategories($child);

                    $sw_menu_cat_label = $cat_model->getData('sw_menu_cat_label');
                    $sw_menu_icon_img = $cat_model->getData('sw_menu_icon_img');
                    $sw_menu_font_icon = $cat_model->getData('sw_menu_font_icon');

                    $item_class = 'level'.$level.' ';
                    if(count($sub_children) > 0)
                        $item_class .= 'parent ';
                    $html .= '<li class="ui-menu-item '.$item_class.'">';
                    if(count($sub_children) > 0) {
                        $html .= '<div class="open-children-toggle"></div>';
                    }
                    if($level == 1 && $sw_menu_icon_img) {
                        $html .= '<div class="menu-thumb-img"><a class="menu-thumb-link" href="'.$this->getCategoryUrl($child).'"><img src="' . $this->_helper->getBaseUrl().'' . $sw_menu_icon_img . '" alt="'.$child->getName().'"/></a></div>';
                    }
                    $html .= '<a href="'.$this->getCategoryUrl($child).'" title="'.$child->getName().'">';
                    if ($level > 1 && $sw_menu_icon_img)
                        $html .= '<img class="menu-thumb-icon" src="' . $this->_helper->getBaseUrl().'' . $sw_menu_icon_img . '" alt="'.$child->getName().'"/>';
                    elseif($sw_menu_font_icon)
                        $html .= '<em class="menu-thumb-icon '.$sw_menu_font_icon.'"></em>';
                    $html .= '<span>'.$child->getName();
                    if($sw_menu_cat_label)
                        $html .= '<span class="cat-label cat-label-'.$sw_menu_cat_label.'">'.$this->_megamenuConfig['cat_labels'][$sw_menu_cat_label].'</span>';
                    $html .= '</span></a>';
                    if(count($sub_children) > 0) {
                        $html .= $this->getSubmenuItemsHtml($sub_children, $level+1, $max_level, $column_width, $menu_type);
                    }
                    $html .= '</li>';
                }
            }
            $html .= '</ul>';
        }

        return $html;
    }

    public function getMegamenuHtml()
    {
        $html = '';

        $categories = $this->getStoreCategories(true,false,true);

        $this->_megamenuConfig = $this->_helper->getConfig('sw_megamenu');

        $max_level = $this->_megamenuConfig['general']['max_level'];
        $html .= $this->getCustomBlockHtml('before');
        foreach($categories as $category) {
            if (!$category->getIsActive()) {
                continue;
            }

            $cat_model = $this->getCategoryModel($category->getId());

            $sw_menu_hide_item = $cat_model->getData('sw_menu_hide_item');

            if(!$sw_menu_hide_item) {
                $children = $this->getActiveChildCategories($category);
                $sw_menu_cat_label = $cat_model->getData('sw_menu_cat_label');
                $sw_menu_icon_img = $cat_model->getData('sw_menu_icon_img');
                $sw_menu_font_icon = $cat_model->getData('sw_menu_font_icon');
                $sw_menu_cat_columns = $cat_model->getData('sw_menu_cat_columns');
                $sw_menu_float_type = $cat_model->getData('sw_menu_float_type');

                if(!$sw_menu_cat_columns){
                    $sw_menu_cat_columns = 4;
                }

                $menu_type = $cat_model->getData('sw_menu_type');
                if(!$menu_type)
                    $menu_type = $this->_megamenuConfig['general']['menu_type'];

                $custom_style = '';
                if($menu_type=="staticwidth")
                    $custom_style = ' style="width: 500px;"';

                $sw_menu_static_width = $cat_model->getData('sw_menu_static_width');
                if($menu_type=="staticwidth" && $sw_menu_static_width)
                    $custom_style = ' style="width: '.$sw_menu_static_width.';"';

                $item_class = 'level0 ';
                $item_class .= $menu_type.' ';

                $menu_top_content = $cat_model->getData('sw_menu_block_top_content');
                $menu_left_content = $cat_model->getData('sw_menu_block_left_content');
                $menu_left_width = $cat_model->getData('sw_menu_block_left_width');
                if(!$menu_left_content || !$menu_left_width)
                    $menu_left_width = 0;
                $menu_right_content = $cat_model->getData('sw_menu_block_right_content');
                $menu_right_width = $cat_model->getData('sw_menu_block_right_width');
                if(!$menu_right_content || !$menu_right_width)
                    $menu_right_width = 0;
                $menu_bottom_content = $cat_model->getData('sw_menu_block_bottom_content');
                if($sw_menu_float_type)
                    $sw_menu_float_type = 'fl-'.$sw_menu_float_type.' ';
                if(count($children) > 0 || (($menu_type=="fullwidth" || $menu_type=="staticwidth") && ($menu_top_content || $menu_left_content || $menu_right_content || $menu_bottom_content)))
                    $item_class .= 'parent ';
                $html .= '<li class="ui-menu-item '.$item_class.$sw_menu_float_type.'">';
                if(count($children) > 0) {
                    $html .= '<div class="open-children-toggle"></div>';
                }
                $html .= '<a href="'.$this->getCategoryUrl($category).'" class="level-top" title="'.$category->getName().'">';
                if ($sw_menu_icon_img)
                    $html .= '<img class="menu-thumb-icon" src="' . $this->_helper->getBaseUrl().'' . $sw_menu_icon_img . '" alt="'.$category->getName().'"/>';
                elseif($sw_menu_font_icon)
                    $html .= '<em class="menu-thumb-icon '.$sw_menu_font_icon.'"></em>';
                $html .= '<span>'.$category->getName().'</span>';
                if($sw_menu_cat_label)
                    $html .= '<span class="cat-label cat-label-'.$sw_menu_cat_label.'">'.$this->_megamenuConfig['cat_labels'][$sw_menu_cat_label].'</span>';
                $html .= '</a>';
                if(count($children) > 0 || (($menu_type=="fullwidth" || $menu_type=="staticwidth") && ($menu_top_content || $menu_left_content || $menu_right_content || $menu_bottom_content))) {
                    $html .= '<div class="level0 submenu"'.$custom_style.'>';
                    if(($menu_type=="fullwidth" || $menu_type=="staticwidth")) {
                        $html .= '<div class="container">';
                    }
                    if(($menu_type=="fullwidth" || $menu_type=="staticwidth") && $menu_top_content) {
                        $html .= '<div class="menu-top-block">'.$this->getBlockContent($menu_top_content).'</div>';
                    }
                    if(count($children) > 0 || (($menu_type=="fullwidth" || $menu_type=="staticwidth") && ($menu_left_content || $menu_right_content))) {
                        $html .= '<div class="row">';
                        if(($menu_type=="fullwidth" || $menu_type=="staticwidth") && $menu_left_content && $menu_left_width > 0) {
                            $html .= '<div class="menu-left-block col-md-'.$menu_left_width.'">'.$this->getBlockContent($menu_left_content).'</div>';
                        }
                        $html .= $this->getSubmenuItemsHtml($children, 1, $max_level, 12-$menu_left_width-$menu_right_width, $menu_type, $sw_menu_cat_columns);
                        if(($menu_type=="fullwidth" || $menu_type=="staticwidth") && $menu_right_content && $menu_right_width > 0) {
                            $html .= '<div class="menu-right-block col-md-'.$menu_right_width.'">'.$this->getBlockContent($menu_right_content).'</div>';
                        }
                        $html .= '</div>';
                    }
                    if(($menu_type=="fullwidth" || $menu_type=="staticwidth") && $menu_bottom_content) {
                        $html .= '<div class="menu-bottom-block">'.$this->getBlockContent($menu_bottom_content).'</div>';
                    }
                    if(($menu_type=="fullwidth" || $menu_type=="staticwidth")) {
                        $html .= '</div>';
                    }
                    $html .= '</div>';
                }
                $html .= '</li>';
            }
        }
        $html .= $this->getCustomBlockHtml('after');

        return $html;
    }	
}

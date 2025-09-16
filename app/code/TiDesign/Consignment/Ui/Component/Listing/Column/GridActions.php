<?php

namespace TiDesign\Consignment\Ui\Component\Listing\Column;

class GridActions extends \Magento\Ui\Component\Listing\Columns\Column
{

	private $urlBuilder;
    const URL_PATH_EDIT 	= 'consignment/consignmentlist/edit';
    const URL_PATH_DELETE 	= 'consignment/consignmentlist/delete';
    const URL_PATH_DETAILS 	= 'consignment/consignmentlist/details';
	const URL_LOGIN_AS		= 'mploginascustomer/login/index';

    /**
     * @param \Magento\Framework\View\Element\UiComponent\ContextInterface $context
     * @param \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory,
        \Magento\Framework\UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                if (isset($item['td_id'])) {
                    $item[$this->getData('name')] = [
                        'edit' => [
                            'href' => $this->urlBuilder->getUrl(
                                static::URL_PATH_EDIT,
                                [
                                    'id' => $item['td_id']
                                ]
                            ),
                            'label' => __('Edit')
                        ],
                        'delete' => [
                            'href' => $this->urlBuilder->getUrl(
                                static::URL_PATH_DELETE,
                                [
                                    'id' => $item['td_id']
                                ]
                            ),
                            'label' => __('Delete'),
                            'confirm' => [
                                'title' => __('Delete'),
                                'message' => __('Are you sure you wan\'t to delete record?')
                            ]
                        ],
						'login' => [
                            'href' => $this->urlBuilder->getUrl(
                                static::URL_LOGIN_AS,
                                [
                                    'id' => $item['td_customer']
                                ]
                            ),
                            'label' => __('Login as Customer'),
							'target' => '_blank',
                        ]
                    ];
                }
            }
        }
        
        return $dataSource;
    }
}

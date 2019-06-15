<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Simi\Simiconnector\Model\Resolver\Products\DataProvider;

use Magento\Catalog\Model\Product\Visibility;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Catalog\Api\Data\ProductSearchResultsInterfaceFactory;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\CatalogGraphQl\Model\Resolver\Products\DataProvider\Product\CollectionProcessorInterface;

/**
 * Product field data provider, used for GraphQL resolver processing.
 */
class Product
{
    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var ProductSearchResultsInterfaceFactory
     */
    private $searchResultsFactory;

    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * @var Visibility
     */
    private $visibility;
    private $simiObjectManager;

    /**
     * @param CollectionFactory $collectionFactory
     * @param ProductSearchResultsInterfaceFactory $searchResultsFactory
     * @param Visibility $visibility
     * @param CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        ProductSearchResultsInterfaceFactory $searchResultsFactory,
        Visibility $visibility,
        CollectionProcessorInterface $collectionProcessor,
        \Magento\Framework\ObjectManagerInterface $simiObjectManager //simiconnector changing
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->visibility = $visibility;
        $this->collectionProcessor = $collectionProcessor;
        $this->simiObjectManager = $simiObjectManager; //simiconnector changing
    }

    /**
     * Gets list of product data with full data set. Adds eav attributes to result set from passed in array
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @param string[] $attributes
     * @param bool $isSearch
     * @param bool $isChildSearch
     * @return SearchResultsInterface
     */
    public function getList(
        array $args, //simiconnector changing
        SearchCriteriaInterface $searchCriteria,
        array $attributes = [],
        bool $isSearch = false,
        bool $isChildSearch = false
    ): SearchResultsInterface {
        /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $collection */
        $collection = $this->collectionFactory->create();
        /*
         * simiconnector changing
        */
        $collection->addAttributeToSelect('*');
        $helper = $this->simiObjectManager->get('Simi\Simiconnector\Helper\Products');
        $helper->builderQuery = $collection;
        $params = array(
            'filter' => array()
        );
        //apply filter
        if ($args && isset($args['simiFilter']) && $simiFilter = json_decode($args['simiFilter'], true)) {
            $cat_filtered = false;
            if (isset($simiFilter['cat'])) {
                $simiFilter['category_id'] = $simiFilter['cat'];
                unset($simiFilter['cat']);
            }
            $params = array(
                'filter' => array(
                    'layer' => $simiFilter
                )
            );
            $helper->filterCollectionByAttribute($collection, $params, $cat_filtered);
        }

        //get filter options
        if ($simiProductFilters = $helper->getLayerNavigator($collection, $params)) {
            $simiFilterOptions = array();
            if (isset($simiProductFilters['layer_filter'])) {
                foreach ($simiProductFilters['layer_filter'] as $layer_filter) {
                    if (isset($layer_filter['filter']) && $count = count($layer_filter['filter'])) {
                        $filtersubOptions = array();
                        foreach ($layer_filter['filter'] as $filtersubOption) {
                            $filtersubOption['value_string'] = (string) $filtersubOption['value'];
                            $filtersubOptions[] = $filtersubOption;
                        }
                        $simiFilterOptions[] = array(
                            'name' => $layer_filter['title'],
                            'filter_items_count' => $count,
                            'request_var' => $layer_filter['attribute'],
                            'filter_items' => $filtersubOptions,
                        );
                    }
                }
            }

            if (count($simiFilterOptions)) {
                $registry = $this->simiObjectManager->get('\Magento\Framework\Registry');
                $registry->register('simiProductFilters', json_encode($simiFilterOptions));
            }
        }
        /*
         * end
        */
        $this->collectionProcessor->process($collection, $searchCriteria, $attributes);

        if (!$isChildSearch) {
            $visibilityIds = $isSearch
                ? $this->visibility->getVisibleInSearchIds()
                : $this->visibility->getVisibleInCatalogIds();
            $collection->setVisibility($visibilityIds);
        }
        $collection->load();

        // Methods that perform extra fetches post-load
        if (in_array('media_gallery_entries', $attributes)) {
            $collection->addMediaGalleryData();
        }
        if (in_array('options', $attributes)) {
            $collection->addOptionsToResult();
        }

        $searchResult = $this->searchResultsFactory->create();
        $searchResult->setSearchCriteria($searchCriteria);
        $searchResult->setItems($collection->getItems());
        $searchResult->setTotalCount($collection->getSize());
        return $searchResult;
    }
}

<?php
namespace Simi\Simiconnector\Block\Adminhtml\Siminotification\Edit\Tab;

/**
 * Cms page edit form main tab
 */
class Main extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

    /**
     * @var \Simi\Simiconnector\Helper\Website
     **/
    protected $_websiteHelper;

    /**
     * @var \Simi\Simiconnector\Model\Siminotification
     */
    protected $_siminotificationFactory;

    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    protected $_jsonEncoder;

    /**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    protected $_categoryFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \Simi\Simiconnector\Helper\Website $websiteHelper,
        \Simi\Simiconnector\Model\SiminotificationFactory $siminotificationFactory,

        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        array $data = []
    )
    {
        $this->_siminotificationFactory = $siminotificationFactory;
        $this->_websiteHelper = $websiteHelper;
        $this->_systemStore = $systemStore;
        $this->_jsonEncoder = $jsonEncoder;
        $this->_categoryFactory = $categoryFactory;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        /* @var $model \Magento\Cms\Model\Page */
        $model = $this->_coreRegistry->registry('siminotification');

        /*
         * Checking if user have permissions to save information
         */
        if ($this->_isAllowedAction('Simi_Simiconnector::siminotification_save')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('');
        $htmlIdPrefix = $form->getHtmlIdPrefix();

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Siminotification Information')]);

        $new_category_parent = false;
        if ($model->getId()) {
            $fieldset->addField('siminotification_id', 'hidden', ['name' => 'siminotification_id']);
            $new_category_parent = $model->getData('category_id');
        }

        $fieldset->addField(
            'website_id',
            'select',
            [
                'name' => 'website_id',
                'label' => __('Website'),
                'title' => __('Website'),
                'required' => true,
                'disabled' => $isElementDisabled,
                'options' => $this->_siminotificationFactory->create()->toOptionWebsiteHash(),
            ]
        );

        $fieldset->addField(
            'siminotification_sanbox',
            'select',
            [
                'name' => 'siminotification_sanbox',
                'label' => __('Send To'),
                'title' => __('Send To'),
                'required' => true,
                'disabled' => $isElementDisabled,
                'options' => $this->_siminotificationFactory->create()->toOptionSanboxHash(),
            ]
        );

        $fieldset->addField(
            'show_popup',
            'select',
            [
                'name' => 'show_popup',
                'label' => __('Show Popup'),
                'title' => __('Show Popup'),
                'required' => true,
                'disabled' => $isElementDisabled,
                'options' => $this->_siminotificationFactory->create()->toOptionPopupHash(),
            ]
        );

        $fieldset->addField(
            'siminotification_title',
            'text',
            [
                'name' => 'siminotification_title',
                'label' => __('Title'),
                'title' => __('Title'),
                'required' => true,
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'image_url',
            'image',
            [
                'name' => 'image_url',
                'label' => __('Image'),
                'title' => __('Image'),
                'required' => false,
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'siminotification_content',
            'textarea',
            [
                'name' => 'siminotification_content',
                'label' => __('Message'),
                'title' => __('Message'),
                'required' => false,
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'type',
            'select',
            [
                'name' => 'type',
                'label' => __('Direct viewers to'),
                'title' => __('Direct viewers to'),
                'required' => true,
                'disabled' => $isElementDisabled,
                'options' => $this->_siminotificationFactory->create()->toOptionTypeHash(),
                'onchange' => 'changeType(this.value)',
            ]
        );

        /* product + category + url */
        $fieldset->addField(
            'product_id',
            'text',
            [
                'name' => 'product_id',
                'label' => __('Product ID'),
                'title' => __('Product ID'),
                'required' => true,
                'disabled' => $isElementDisabled,
                'after_element_html' => '<a href="#" title="Show Product Grid" onclick="toogleProduct();return false;"><img id="show_product_grid" src="'.$this->getViewFileUrl('Simi_Simiconnector::images/arrow_down.png').'" title="" /></a>'.$this->getLayout()->createBlock('Simi\Simiconnector\Block\Adminhtml\Siminotification\Edit\Tab\Productgrid')->toHtml()
            ]
        );

        $fieldset->addField(
            'new_category_parent',
            'select',
            [
                'label' => __('Categories'),
                'title' => __('Categories'),
                'required' => true,
                'class' => 'validate-parent-category',
                'name' => 'new_category_parent',
                'options' => $this->_getParentCategoryOptions($new_category_parent),
            ]
        );

        $fieldset->addField(
            'siminotification_url',
            'textarea',
            [
                'name' => 'siminotification_url',
                'label' => __('Url'),
                'title' => __('Url'),
                'required' => true,
                'disabled' => $isElementDisabled,
            ]
        );
        /* product + category + url */

        $fieldset->addField(
            'created_at',
            'label',
            [
                'name' => 'created_at',
                'label' => __('Created At'),
                'title' => __('Created At'),
                'required' => false,
                'disabled' => $isElementDisabled,
            ]
        );

        $_fieldset = $form->addFieldset('device_location', ['legend' => __('Notification Device & Location')]);

        $_fieldset->addField(
            'device_id',
            'select',
            [
                'name' => 'device_id',
                'label' => __('Device Type'),
                'title' => __('Device Type'),
                'required' => true,
                'disabled' => $isElementDisabled,
                'options' => $this->_siminotificationFactory->create()->toOptionDeviceHash(),
            ]
        );

        $_fieldset->addField(
            'address',
            'text',
            [
                'name' => 'address',
                'label' => __('Address'),
                'title' => __('Address'),
                'required' => false,
                'disabled' => $isElementDisabled,
            ]
        );

        $_fieldset->addField(
            'country',
            'select',
            [
                'name' => 'country',
                'label' => __('Country'),
                'title' => __('Country'),
                'required' => false,
                'disabled' => $isElementDisabled,
                'options' => $this->_siminotificationFactory->create()->toOptionCountryHash(),
            ]
        );

        $_fieldset->addField(
            'state',
            'text',
            [
                'name' => 'state',
                'label' => __('State/Province'),
                'title' => __('State/Province'),
                'required' => false,
                'disabled' => $isElementDisabled,
            ]
        );

        $_fieldset->addField(
            'city',
            'text',
            [
                'name' => 'city',
                'label' => __('City'),
                'title' => __('City'),
                'required' => false,
                'disabled' => $isElementDisabled,
            ]
        );

        $_fieldset->addField(
            'zipcode',
            'text',
            [
                'name' => 'zipcode',
                'label' => __('Zip Code'),
                'title' => __('Zip Code'),
                'required' => false,
                'disabled' => $isElementDisabled,
            ]
        );

        $this->_eventManager->dispatch('adminhtml_siminotification_edit_tab_main_prepare_form', ['form' => $form]);

        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Get parent category options
     *
     * @return array
     */
    protected function _getParentCategoryOptions($category_id)
    {

        $items = $this->_categoryFactory->create()->getCollection()->addAttributeToSelect(
            'name'
        )->addAttributeToSort(
            'entity_id',
            'ASC'
        )->setPageSize(
            3
        )->load()->getItems();

        $result = [];
        if (count($items) === 2) {
            $item = array_pop($items);
            $result = [$item->getEntityId() => $item->getName()];
        }

        if(sizeof($result) == 0 && $category_id){
            $category = $this->_categoryFactory->create()->load($category_id);
            $result = [$category_id => $category->getName()];
        }

        return $result;
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Siminotification Information');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Siminotification Information');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }

}

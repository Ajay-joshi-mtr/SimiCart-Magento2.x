<?php

namespace Simi\Simiconnector\Controller\Adminhtml\Cms;

use Magento\Backend\App\Action;

class Edit extends \Magento\Backend\App\Action
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;
	
	/**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param Action\Context $context
	 * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(Action\Context $context, \Magento\Framework\View\Result\PageFactory $resultPageFactory, \Magento\Framework\Registry $registry)
    {
        $this->resultPageFactory = $resultPageFactory;
		$this->_coreRegistry = $registry;
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Simi_Simiconnector::save');
    }

    /**
     * Init actions
     *
     * @return $this
     */
    protected function _initAction()
    {
        // load layout, set active menu and breadcrumbs
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu(
            'Simi_Simiconnector::simiconnector_manage'
        )->addBreadcrumb(
            __('Cms'),
            __('Cms')
        )->addBreadcrumb(
            __('Manage Cms'),
            __('Manage Cms')
        );
        return $resultPage;
    }

    /**
     * Edit CMS page
     *
     * @return void
     */
    public function execute()
    {
        die('1');
        // 1. Get ID and create model
        $id = $this->getRequest()->getParam('cms_id');
        $model = $this->_objectManager->create('Simi\Simiconnector\Model\Cms');

        // 2. Initial checking
        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addError(__('This cms no longer exists.'));
				/** \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }

        // 3. Set entered data if was error when we do save
        $data = $this->_objectManager->get('Magento\Backend\Model\Session')->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }

        // 4. Register model to use later in blocks
        $this->_coreRegistry->register('cms', $model);

        // 5. Build edit form
		/** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->_initAction();
        $resultPage->addBreadcrumb(
            $id ? __('Edit Cms') : __('New Cms'),
            $id ? __('Edit Cms') : __('New Cms')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Cms'));
        $resultPage->getConfig()->getTitle()
            ->prepend($model->getId() ? $model->getId() : __('New Cms'));
        return $resultPage;
    }
}

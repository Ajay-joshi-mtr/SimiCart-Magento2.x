<?php

namespace MobileApp\Connector\Controller\Adminhtml\Banner;

use Magento\Backend\App\Action;

class Save extends \Magento\Backend\App\Action
{
    /**
     * @var PostDataProcessor
     */
    protected $dataProcessor;

    /**
     * @param Action\Context $context
     * @param PostDataProcessor $dataProcessor
     */
    public function __construct(Action\Context $context, PostDataProcessor $dataProcessor)
    {
        $this->dataProcessor = $dataProcessor;
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('MobileApp_Connector::banner_save');
    }

    /**
     * Save action
     *
     * @return void
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        if ($data) {
            $data = $this->dataProcessor->filter($data);
            $model = $this->_objectManager->create('MobileApp\Connector\Model\Banner');

            $id = $this->getRequest()->getParam('banner_id');
            if ($id) {
                $model->load($id);
            }
            if(isset($data['new_category_parent']))
                $data['category_id'] = $data['new_category_parent'];

            $is_delete_banner = isset($data['banner_name']['delete']) ? $data['banner_name']['delete'] : false;
            $data['banner_name'] = isset($data['banner_name']['value']) ? $data['banner_name']['value'] : '';
            $model->addData($data);

            if (!$this->dataProcessor->validate($data)) {
                $this->_redirect('*/*/edit', ['banner_id' => $model->getId(), '_current' => true]);
                return;
            }

            try {
                $imageHelper = $this->_objectManager->get('MobileApp\Connector\Helper\Data');
                if ($is_delete_banner && $model->getBannerName()) {
                    $model->setBannerName('');
                } else {
                    $imageFile = $imageHelper->uploadImage('banner_name','banner');
                    if ($imageFile) {
                        $model->setBannerName($imageFile);
                    }
                }

                $model->save();
                $this->messageManager->addSuccess(__('The Data has been saved.'));
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', ['banner_id' => $model->getId(), '_current' => true]);
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (\Magento\Framework\Model\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the data.'));
            }

            $this->_getSession()->setFormData($data);
            $this->_redirect('*/*/edit', ['banner_id' => $this->getRequest()->getParam('banner_id')]);
            return;
        }
        $this->_redirect('*/*/');
    }
}
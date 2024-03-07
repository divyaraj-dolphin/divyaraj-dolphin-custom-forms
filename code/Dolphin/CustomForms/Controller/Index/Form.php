<?php

namespace Dolphin\CustomForms\Controller\Index;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\Framework\App\Filesystem\DirectoryList;

class Form extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Magento\MediaStorage\Model\File\UploaderFactory
     */
    protected $uploaderFactory;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $filesystem;

    /**
     * @param Context        $context
     * @param PageFactory    $resultPageFactory
     * @param UploaderFactory $uploaderFactory
     * @param \Magento\Framework\Filesystem $filesystem
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        UploaderFactory $uploaderFactory,
        \Magento\Framework\Filesystem $filesystem
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->uploaderFactory = $uploaderFactory;
        $this->filesystem = $filesystem;
    }

    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend(__('Form'));

        if ($this->getRequest()->isPost()) {
            try {
                $uploader = $this->uploaderFactory->create(['fileId' => 'file_upload']);
                $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
                $uploader->setAllowRenameFiles(true);
                $uploader->setFilesDispersion(true);
                $uploader->setAllowCreateFolders(true);

                $mediaDirectory = $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA);

                $result = $uploader->save($mediaDirectory->getAbsolutePath('custom_forms/uploads'));

                $filePath = 'custom_forms/uploads' . $result['file'];
                // Process your file as needed, e.g., save file path to database

                $this->messageManager->addSuccessMessage(__('File uploaded successfully.'));
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(__('Error uploading file.'));
            }
        }

        return $resultPage;
    }
}

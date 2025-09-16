<?php

    namespace TiDesign\Fundraiser\Controller\Ajax;

    use Magento\Framework\App\Action\Action;
    use Magento\Framework\App\Action\Context;
    use Magento\Framework\App\Filesystem\DirectoryList;
    use Magento\Framework\Exception\LocalizedException;
    use Magento\MediaStorage\Model\File\UploaderFactory;
    use Magento\Framework\Filesystem;
    use Magento\Framework\Controller\Result\JsonFactory;

    class Image extends Action
    {
        private $resultJsonFactory;
        protected $filesystem;


        public function __construct(
            Context $context,
            JsonFactory $resultJsonFactory,
            Filesystem $filesystem
        ) {
            $this->resultJsonFactory = $resultJsonFactory;
            $this->filesystem = $filesystem;
            parent::__construct($context);
        }
        public function execute(){
            $data = $this->getRequest()->getParams();
            $jsonResult = $this->resultJsonFactory->create();
            try {
                $img = $_POST['base64data'];
                $img = str_replace('data:image/png;base64,', '', $img);
                $img = str_replace(' ', '+', $img);
                $data = base64_decode($img);

                $mediaDirectory = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);
                $imagePath = $mediaDirectory->getAbsolutePath('TiDesign/Fundraiser/Render');

                $file = $imagePath . uniqid() . '.png';
                $success = file_put_contents($file, $data);
                return $jsonResult->setData(['success' => true, 'image_url' => $file]);
            } catch (\Exception $e) {
                return $jsonResult->setData(['success' => false, 'message' => $e->getMessage()]);
            }
        }
    }

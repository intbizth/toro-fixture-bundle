<?php

namespace Toro\Bundle\FixtureBundle\DataFixture\Uploader;

use Doctrine\ODM\PHPCR\DocumentManagerInterface;
use Symfony\Cmf\Bundle\MediaBundle\File\UploadFileHelperInterface;
use Symfony\Cmf\Bundle\MediaBundle\FileInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\KernelInterface;

class ImageUploadHelper
{
    /**
     * @var KernelInterface
     */
    private $kernel;

    /**
     * @var DocumentManagerInterface
     */
    private $documentManager;

    /**
     * @var UploadFileHelperInterface
     */
    private $helper;

    /**
     * @var string
     */
    private $dataClass;

    public function __construct(
        KernelInterface $kernel,
        DocumentManagerInterface $documentManager,
        UploadFileHelperInterface $uploadFileHelper,
        $dataClass
    ) {
        $this->kernel = $kernel;
        $this->documentManager = $documentManager;
        $this->helper = $uploadFileHelper;
        $this->dataClass = $dataClass;
    }

    /**
     * @param string $file File path
     * @param boolean $rename
     *
     * @return null|\Symfony\Cmf\Bundle\MediaBundle\FileInterface
     */
    public function upload($file, $rename = true)
    {
        if (!$file) {
            return null;
        }

        $ext = explode('.', $file);
        $ext = $ext[count($ext) - 1];

        // remote file
        if (preg_match('/\/\//', $file)) {
            @mkdir('/tmp/toro-fixtures');
            $fileTemp = sprintf('/tmp/toro-fixtures/%s.%s', uniqid(time()), $ext);
            file_put_contents($fileTemp, file_get_contents($file));
            echo "Downloaded - " . $file . PHP_EOL;
        } else {
            $fileTemp = $this->kernel->locateResource($file);
            echo "Located - " . $file . PHP_EOL;
        }

        if ($rename) {
            $fileName = uniqid('sys-') . '.' . $ext;
        } else {
            $fileName = explode('/', $fileTemp);
            $fileName = $fileName[count($fileName) - 1];
        }

        $file = new UploadedFile($fileTemp, $fileName, null, null, null, true);
        $file = $this->helper->handleUploadedFile($file, $this->dataClass);

        return $file;
    }

    public function remove(FileInterface $file = null)
    {
        if ($file) {
            $this->documentManager->remove($file);
            $this->documentManager->flush($file);
        }
    }
}

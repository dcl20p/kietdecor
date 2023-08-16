<?php

declare(strict_types=1);

namespace Upload\Controller;

use Laminas\Http\PhpEnvironment\Response;
use Laminas\View\Model\JsonModel;
use Models\Entities\Project;
use \Zf\Ext\Controller\ZfController;
use Laminas\View\Model\ViewModel;

class IndexController extends ZfController
{
    use \ImageTraits\Controller\UploadImages;
    /**
     * List action
     *
     * @return ViewModel
     */
    public function uploadAction(): JsonModel
    {
        $result = [];
        try {
            if ($this->isPostRequest()) {
                $folderName = $this->getParamsQuery('path', 'project');
                dd($this->getParamsFiles());
                if (empty($files = $this->getParamsFiles()['file'] ?? [])) {
                    return new JsonModel([
                        'success' => false,
                        'msg' => $this->mvcTranslate(ZF_MSG_WENT_WRONG)
                    ]);
                }
                foreach ($files as $file) {
                    if (!empty($this->isValidUploadImg($file))) {
                        $upload = $this->uploadImageDropzone(
                            $file, 
                            $folderName, 
                            Project::PROJECT_IMAGE_SIZES
                        );
                        if ($upload) {
                            $result[] = $upload['name'];
                        }
                    }
                }
            } else {
                return new JsonModel([
                    'success' => false,
                    'msg' => $this->mvcTranslate(ZF_MSG_NOT_ALLOW)
                ]);
            }
            
        } catch (\Throwable $e) {
            if (!empty($upload['name'])) {
                $this->revertUploadImageDropzone(
                    $upload['name'], 
                    $folderName, 
                    Project::PROJECT_IMAGE_SIZES
                );
            }
            dd($e->getMessage(), $e->getTraceAsString());
            $this->saveErrorLog($e);
        }
dd($result);
        return new JsonModel([
            'success' => true,
            'msg'     => 'Successful.',
            'data'    => @json_encode($result ?? [])
        ]);
    }
}

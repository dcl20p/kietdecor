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
        $results = [];
        try {
            if ($this->isPostRequest()) {
                $folderName = $this->getParamsQuery('path', Project::FOLDER_IMAGE);
                $sizes      = $this->getParamsQuery('sizes', Project::PROJECT_THUMBNAIL_SIZES);
                $uid        = $this->getParamsQuery('uid', '');

                if (empty($files = $this->getParamsFiles()['file'] ?? [])) {
                    return new JsonModel([
                        'success' => false,
                        'msg' => $this->mvcTranslate(ZF_MSG_WENT_WRONG)
                    ]);
                }

                foreach ($files as $file) {
                    if (!empty($this->isValidUploadImg($file))) {
                        $response = $this->uploadImageDropzone($file, $folderName, $sizes, $uid);
                        if ($response['success']) {
                            $results[] = $response['name'];
                        } else {
                            if (!empty($results)) {
                                foreach ($results as $upload)
                                $this->revertUploadImageDropzone($upload['name'],  $folderName, $sizes);
                            }
                            return new JsonModel([
                                'success' => false,
                                'msg' => $this->mvcTranslate($response['msg'] ?? '')
                            ]);
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
            dd($e->getMessage(), $e->getTraceAsString());
            if (!empty($results)) {
                foreach ($results as $upload)
                $this->revertUploadImageDropzone($upload['name'],  $folderName, $sizes);
            }
            $this->saveErrorLog($e);
        }

        return new JsonModel([
            'success' => true,
            'msg'     => 'Successful.',
            'data'    => implode(',', $results ?? [])
        ]);
    }

    /**
     * Remove file upload
     *
     * @return JsonModel
     */
    public function removeImageAction(): JsonModel
    {
        $isError = false;
        try {
            if ($this->isPostRequest()) {
                $fileName = $this->getParamsPayload('file', '');
                $path     = $this->getParamsPayload('path', Project::FOLDER_IMAGE);
                
                if (empty($fileName)) {
                    return new JsonModel([
                        'success' => false,
                        'msg' => $this->mvcTranslate('Xoá thất bại')
                    ]);
                }
    
                $this->revertUploadImageDropzone($fileName,  $path);
            } else {
                return new JsonModel([
                    'success' => false,
                    'msg' => $this->mvcTranslate(ZF_MSG_NOT_ALLOW)
                ]);
            }
        } catch (\Throwable $e) {
            $isError = true;
            $this->saveErrorLog($e);
        }

        return new JsonModel([
            'success' => $isError,
            'msg'     => $isError 
                ? $this->mvcTranslate(ZF_MSG_WENT_WRONG)
                : $this->mvcTranslate('Xoá thành công.')
        ]);
    }
}

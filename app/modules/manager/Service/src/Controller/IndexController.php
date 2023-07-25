<?php

declare(strict_types=1);

namespace Service\Controller;

use Models\Entities\Service;
use \Zf\Ext\Controller\ZfController;
use Laminas\View\Model\ViewModel;
use Laminas\View\Model\JsonModel;

class IndexController extends ZfController
{
    public function indexAction()
    {
        try {
        } catch (\Throwable $e) {
            $this->saveErrorLog($e);
            
        }
        
        return new ViewModel([
            'routeName'     => $this->getCurrentRouteName(),
            'pageTitle'     => $this->mvcTranslate('Tất cả dịch vụ'),
            'activeItemId'  => 'service'
        ]);
    }

    /**
     * Validate param post
     *
     * @param array $params
     * @return array|false
     */
    protected function validData(array $params): array|false
    {
        $params = array_intersect_key($params, [
            'title'       => '',
            'description' => '',
            'status'      => 0
        ]);

        $params['title'] = trim(mb_substr($params['title'], 0, 100));
        $params['description'] = trim(mb_substr($params['title'], 0, 2048));
        $params['status'] = (int) $params['status'];

        if ($params['title'] !== '' || $params['description'] !== '') {
            $this->addErrorMessage(
                $this->mvcTranslate('ZF_MSG_REQUIRE_DATA')
            );
            return false;
        }

        return $params;
    }

    public function addAction()
    {
        try {
            $repo = $this->getEntityRepo(Service::class);
            if ($this->isPostRequest()) {
                dd($this->getParamsQuery(), $this->getParamsPost(), $this->getParamsPayload(), $this->getParamsFiles(), $_GET['file']);

                if ($dataPost = $this->validData($this->getParamsPost())) {
                    dd($dataPost);
                }
            }
        } catch (\Throwable $e) {
            $this->saveErrorLog($e);
            $this->addErrorMessage(
                $this->mvcTranslate(ZF_MSG_WENT_WRONG)
            );
            dd($e->getMessage(), $e->getTraceAsString());
        }
        return new ViewModel([
            'pageTitle'     => $this->mvcTranslate('Thêm dịch vụ'),
            'routeName'     => $this->getCurrentRouteName(),
            'activeItemId'  => 'service'
        ]);
    }

    public function deleteAction()
    {
        $this->addSuccessMessage('Xoá thành công');
        return $this->zfRedirect()->toCurrentRoute(
            [], ['useOldQuery' => true]
        );
    }
}

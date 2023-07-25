<?php

declare(strict_types=1);

namespace Project\Controller;

use \Zf\Ext\Controller\ZfController;
use Laminas\View\Model\ViewModel;
use Laminas\View\Model\JsonModel;

class IndexController extends ZfController
{
    public function indexAction()
    {
        try {
            // dd($this->getAuthen());
        } catch (\Throwable $e) {
            $this->saveErrorLog($e);
            
        }
        
        return new ViewModel([
            'routeName'     => $this->getCurrentRouteName(),
            'pageTitle'     => $this->mvcTranslate('Tất cả dự án'),
            'activeItemId'  => 'project'
        ]);
    }

    public function addAction()
    {
        return new ViewModel([
            'routeName'     => $this->getCurrentRouteName(),
            'pageTitle'     => $this->mvcTranslate('Thêm dự án'),
            'activeItemId'  => 'project'
        ]);
    }

    public function deleteAction()
    {
        $this->addSuccessMessage('Xoá thành công');
        return $this->zfRedirect()->toCurrentRoute(
            [], ['useOldQuery' => true]
        );
    }

    public function listCateAction()
    {
        return new ViewModel([
            'routeName'     => $this->getCurrentRouteName(),
            'pageTitle'     => $this->mvcTranslate('Loại dự án'),
            'activeItemId'  => 'project_cate'
        ]);
    }
}

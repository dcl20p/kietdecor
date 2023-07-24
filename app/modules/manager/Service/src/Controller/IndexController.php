<?php

declare(strict_types=1);

namespace Service\Controller;

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

    public function addAction()
    {
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

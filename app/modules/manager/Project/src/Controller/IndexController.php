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
            'routeName' => $this->getEvent()->getRouteMatch()->getMatchedRouteName(),
            'pageTitle' => $this->mvcTranslate('Tất cả dự án')
        ]);
    }

    public function addAction()
    {
        return new ViewModel([]);
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
        return new ViewModel([]);
    }
}

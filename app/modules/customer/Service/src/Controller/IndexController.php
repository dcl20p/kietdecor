<?php 

declare(strict_types=1);

namespace Service\Controller;
use Laminas\View\Model\ViewModel;
use Zf\Ext\Controller\ZfController;

class IndexController extends ZfController
{
    /**
     * Service page
     *
     * @return ViewModel
     */
    public function indexAction(): ViewModel
    {
        try {

        } catch (\Throwable $e) {
            $this->saveErrorLog($e);
        }
        return new ViewModel([
            'activeItemId' => 'service'
        ]);
    }

    /**
     * Detail service page
     *
     * @return ViewModel
     */
    public function detailAction(): ViewModel
    {
        try {
        } catch (\Throwable $e) {
            $this->saveErrorLog($e);
        }
        return new ViewModel([
            'activeItemId' => 'service'
        ]);
    }
}
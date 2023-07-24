<?php

declare(strict_types=1);

namespace Application\Controller;

use \Zf\Ext\Controller\ZfController;
use Laminas\View\Model\ViewModel;

class IndexController extends ZfController
{
    public function indexAction()
    {
        return new ViewModel();
    }
}

<?php 

declare(strict_types=1);

namespace Application\Controller;
use Laminas\View\Model\ViewModel;
use Zf\Ext\Controller\ZfController;

class IndexController extends ZfController
{
    /**
     * Home page
     *
     * @return ViewModel
     */
    public function indexAction(): ViewModel
    {
        return new ViewModel([
            'activeItemId' => 'home'
        ]);
    }

    /**
     * Price table page
     *
     * @return ViewModel
     */
    public function priceAction(): ViewModel
    {
        return new ViewModel([
            'activeItemId' => 'price'
        ]);
    }

    /**
     * About page
     *
     * @return ViewModel
     */
    public function aboutAction(): ViewModel
    {
        return new ViewModel([
            'activeItemId' => 'about'
        ]);
    }

    /**
     * About page
     *
     * @return ViewModel
     */
    public function contactAction(): ViewModel
    {
        return new ViewModel([
            'activeItemId' => 'contact'
        ]);
    }
    
}
<?php
namespace Application\View\Helper;

use Laminas\View\Helper\AbstractHelper;

/**
 * This view helper class displays a menu bar.
 */
class Toolbars extends AbstractHelper 
{
    public function __invoke() {
        return $this->getView()->render('application/index/toolbar');
    }
}
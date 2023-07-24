<?php
namespace Application\View\Helper;

use Laminas\View\Helper\AbstractHelper;

/**
 * This view helper class displays a menu bar.
 */
class MenuHeader extends AbstractHelper 
{
    /**
     * EntityManager
     * @var \Doctrine\ORM\EntityManager
     */
    protected $_entityManager = null;

    /**
     * Constructor.
     * @param array $items Menu items.
     * @param \Doctrine\ORM\EntityManager $entityManager Entity manager
     */
    public function __construct($entityManager) 
    {
        $this->_entityManager = $entityManager;
    }

    /**
     * Invoke helper
     * 
     * @return string Rendered menu HTML
     */
    public function __invoke() 
    {
        return $this->getView()->render('application/index/menu-header', [
        ]);
    }
}
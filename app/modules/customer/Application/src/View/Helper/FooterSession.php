<?php
namespace Application\View\Helper;
use Doctrine\ORM\EntityManager;
use Laminas\Router\Http\RouteMatch;
use Laminas\View\Helper\AbstractHelper;
use Models\Entities\FEMenu;


class FooterSession extends AbstractHelper
{
    /**
     * EntityManager
     *
     * @var EntityManager
     */
    protected $_entityManager = null;

    /**
     * Route match
     *
     * @var RouteMatch
     */
    protected $_routeMatch = null;

    public function __construct(
        EntityManager $entityManager, ?RouteMatch $routeMatch = null
    ) {
        $this->_entityManager = $entityManager;
        $this->_routeMatch    = $routeMatch;
    }

    public function __invoke(array $config = [])
    {
        if (!empty($this->_routeMatch)) {
            return $this->getView()->render('application/layout/footer', [
                'menuItems' => $this->_entityManager->getRepository(FEMenu::class)
                    ->getTreeDataFromCache([
                        'params' => [
                            'status'    => 1,
                            'domain'    => FEMenu::DOMAIN_CTM,
                            'position'  => FEMenu::POSITION_FOOTER,
                            
                        ]
                ], FEMenu::POSITION_FOOTER),
            ]);
        }
        return '';
    }
}
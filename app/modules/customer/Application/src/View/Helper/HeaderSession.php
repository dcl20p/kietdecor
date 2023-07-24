<?php
namespace Application\View\Helper;
use Doctrine\ORM\EntityManager;
use Laminas\Router\Http\RouteMatch;
use Laminas\View\Helper\AbstractHelper;
use Models\Entities\FEMenu;


class HeaderSession extends AbstractHelper
{
    /**
     * EntityManager
     *
     * @var EntityManager
     */
    protected $_entityManager = null;

    /**
     * Menu header items array.
     * @var array
     */

    protected $items;

    /**
     * Facebook config
     *
     * @var array
     */
    protected $_fbConfig = null;

    /**
     * Active item's ID.
     * @var string|null
     */
    protected $activeItemId = null;

    /**
     * Route match
     *
     * @var RouteMatch
     */
    protected $_routeMatch = null;

    /**
     * Contructor
     *
     * @param EntityManager $entityManager
     * @param array $items
     * @param array|null $fbConfig
     * @param RouteMatch|null $routeMatch
     */
    public function __construct(
        EntityManager $entityManager, array $items = [], ?array $fbConfig = null, ?RouteMatch $routeMatch = null
    ) {
        $this->items = $items;
        $this->_entityManager = $entityManager;
        $this->_fbConfig      = $fbConfig;
        $this->_routeMatch    = $routeMatch;
    }

    /**
     * Sets menu items.
     * @param array $items Menu items.
     */
    public function setItems(array $items): void
    {
        $this->items = $items;
    }

    /**
    * Sets ID of the active items.
    * @param string|null $activeItemId
    */
    public function setActiveItemId(string $activeItemId): void
    {
        $this->activeItemId = $activeItemId;
    }

    public function render(): mixed
    {
        if (!empty($this->_routeMatch)) {
            $result = $resultDesktop = '';
            foreach ($this->items as $item) {
                $method = 'render' . ucfirst($item['render']); 
                $result .= $this->$method($item);
                $resultDesktop .= $this->$method($item, true);
            }
            return $this->getView()->render('application/layout/menu-top', [
                // 'menuItems' => $this->_entityManager->getRepository(FEMenu::class)
                //     ->getTreeDataFromCache([
                //         'params' => [
                //             'status'    => 1,
                //             'domain'    => FEMenu::DOMAIN_CTM,
                //             'position'  => FEMenu::POSITION_HEADER,
                            
                //         ]
                // ], FEMenu::POSITION_HEADER),
                'menuItems' => $result,
                'menuItemsDeskTop' => $resultDesktop,
            ]);
        }
        return '';
    }

    /**
     * Renders an item.
     * @param array $item The menu item info.
     * @param boolean $isDesktop
     * @return string HTML code of the item.
     */
    protected function renderSubs(array $item, bool $isDesktop = false): string
    {
        $subHtml = $active = '';
        $view = $this->getView();
        foreach ($item['subs'] as $sub) {
            $subHtml .= $this->{'render'.ucfirst($sub['render'])}($sub, $isDesktop); 
        }

        if ($this->activeItemId == $item['id']) {
            $active = 'active';
        }

        $link = $item['link'] ?? [];
        $hasChild = !empty($item['subs']) ? true : false;

        $html = '<li class="menu-item '.($hasChild ? 'menu-item-has-children' : '').
            ($isDesktop ? ' inteco-normal-menu' : '') .'">
            <a href="' .($hasChild ? 'javascript:void(0);' : $this->createUrl($link)). '" class="' . $active . 
                ($isDesktop ? ' sf-with-ul-pre' : '') .'">
                ' .$view->translate($item['label']) . '
            </a>';
                
        if ($hasChild) {
            $html .= '<ul class="sub-menu">' . $subHtml . '</ul>';
        }
        $html .= '</li>';
        
        unset($view);
        return $html;
    }

    /**
     * Renders an item.
     * @param array $item The menu item info.
     * @param boolean $isDesktop
     * @return string HTML code of the item.
     */
    protected function renderLink(array $item, bool $isDesktop = false): string
    {
        $link = $item['link'] ?? [];
        $active = '';
        if ($this->activeItemId == $item['id']) {
            $active = 'active';
        }
        return '<li class="menu-item" ' .($isDesktop ? 'data-size="60"' : ''). '>'
            .'<a class="' .$active. '" href="' .$this->createUrl($link). '">'
                .$this->getView()->translate($item['label'])
            .'</a></li>';
    }

     /**
     * Create zfUrl
     * @param array $opts
     * @return string
     */
    protected function createUrl(array $opts = []): string
    {
        if (empty($opts)) { 
            return '';
        }       
        return $this->getView()->zfUrl($opts[0] ?? '', $opts[1] ?? [], $opts[2] ?? []);
    }
}
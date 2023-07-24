<?php
namespace Application\View\Helper;

use Laminas\View\Helper\AbstractHelper;

/**
 * This view helper class displays a menu bar
 */
class MenuLeft extends AbstractHelper
{
    /**
     * Menu items array.
     * @var array
     */
    protected $items;

    /**
     * Active item's ID.
     * @var string|null
     */
    protected $activeItemId;

    /**
     * Authentication
     * @var mixed
     */
    protected $authenticate;

    /**
     * Acl Permissions
     * @var mixed
     */
    protected $aclPermission;

    /**
     * Constructor.
     * @param array $items Menu items.
     * @param mixed $authenticate
     * @param mixed $aclPermission
     */
    public function __construct(array $items = [], $authenticate = null, $aclPermission = null)
    {
        $this->items = $items;
        $this->authenticate = $authenticate;
        $this->aclPermission = $aclPermission;
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
    public function setActiveItemId(?string $activeItemId): void
    {
        $this->activeItemId = $activeItemId;
    }

    /**
     * Renders the menu.
     * @return string HTML code of the menu.
     */
    public function render(): string
    {
        $result = '';
        foreach ($this->items as $item) {
            $method = 'render' . ucfirst($item['render']); 
            $result .= $this->$method($item);
        }
        return $this->getView()->render('application/index/menu-left', [
            'items' => $result,
            'authen' => $this->authenticate
        ]);
    }
  
    /**
     * Check user permission.
     * @param string $routeName
     * @param array $params
     * @return boolean
     */
    protected function checkPermission(string $routeName = '', array $params = []): bool
    {
        if ($this->aclPermission)
            return $this->aclPermission->checkPermission($routeName, $params);
        return true;
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

    /**
     * Renders an item.
     * @param array $item The menu item info.
     * @return string HTML code of the item.
     */
    protected function renderTitle(array $item): string
    {
        return '<li class="nav-item mt-3">
                    <h6 class="ps-4  ms-2 text-uppercase text-xs font-weight-bolder text-white">'
                        . $this->getView()->translate($item['label']) .
                    '</h6>
                </li>';
    }

    /**
     * Renders an item.
     * @param array $item The menu item info.
     * @return string HTML code of the item.
     */
    protected function renderLink(array $item): string
    {
        $link = $item['link'] ?? [];
        // Check for permission on each link
        if (!empty($link) && !$this->checkPermission($link[0], $link[1] ?? [])) {
            return '';
        }
        
        $active = '';
        if ($this->activeItemId == $item['id']) {
            $active = 'active';
        }
        return '<li class="nav-item ' .$active. '">'
            .'<a class="nav-link text-white ' .$active. '" href="' .$this->createUrl($link). '">'
                .'<i class="material-icons-round opacity-10 icon-fs-1">' .$item['icon']. '</i>'
                .'<span class="sidenav-normal ms-2  ps-1">'
                    .$this->getView()->translate($item['label'])
                    .'<b class="caret"></b>'
                .'</span></a></li>';
    }
    
    /**
     * Renders an item.
     * @param array $item The menu item info.
     * @return string HTML code of the item.
     */
    protected function renderSubs(array $item): string
    {
        $subHtml = $active = $expand = ''; $bool = false;
        $view = $this->getView();
        
        foreach ($item['subs'] as $sub) {
            $subHtml .= $this->{'render'.ucfirst($sub['render'])}($sub); 
            if ($this->activeItemId == $sub['id']) {
                $active = 'active';
                $bool = true;
                $expand = 'show';
            }
        }
        
        // No any child
        if ('' == $subHtml) {
            return '';
        }
        $icon = '';
        if ($item['icon']) {
            $icon = '<i class="material-icons-round opacity-10">' .$item['icon']. '</i>';
        }
        $html = '<li class="nav-item">
            <a data-bs-toggle="collapse" href="#' .$item['id'] .'" class="nav-link text-white ' .$active . '"
                aria-controls="' .$item['id'] .'" role="button" aria-expanded="' .$bool . '">
                ' .$icon . '
                <span class="nav-link-text ms-2 ps-1">' .$view->translate($item['label']) . '</span>
            </a>
            <div class="collapse ' .$expand . '" id="' .$item['id'] .'">
                <ul class="nav">';
        
        $html .= $subHtml;
        $html .= '</ul></div></li>';
        
        unset($view);
        return $html;
    }
}
?>
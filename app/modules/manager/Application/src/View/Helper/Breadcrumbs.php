<?php

namespace Application\View\Helper;

use Laminas\View\Helper\AbstractHelper;

/**
 * This view helper class displays breadcrumbs.
 */
class Breadcrumbs extends AbstractHelper
{
    /**
     * Array of items.
     * @var array
     */
    private array $items;
    
    /**
     * Constructor.
     * @param array $items Array of items (optional).
     */
    public function __construct(array $items = [])
    {
        $this->setItems($items);
    }
    
    /**
     * Sets the items.
     * @param array $items Items.
     */
    public function setItems(array $items): void
    {
        $this->items = $items;
    }
    
    /**
     * Renders the breadcrumbs.
     * @return string HTML code of the breadcrumbs.
     */
    public function render(): string
    {
        $itemCount = count($this->items);
        if ($itemCount === 0) {
            return '';
        }
        
        $result = '<ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
            <li class="me-2">
                <i class="material-icons">grid_view</i>
            </li>';
        
        $itemNum = 1;
        foreach ($this->items as $label => $link) {
            $isActive = ($itemNum === $itemCount);
            $result .= $this->renderItem($label, $link, $isActive);
            $itemNum++;
        }
        
        $result .= '</ol>';
        
        return $result;
    }
    
    /**
     * Renders an item.
     * @param string $label
     * @param string $link
     * @param boolean $isActive
     * @return string HTML code of the item.
     */
    protected function renderItem(string $label, string $link, bool $isActive): string
    {
        $escapeHtml = $this->getView()->plugin('escapeHtml');
        
        $result = $isActive 
            ? '<li class="breadcrumb-item text-sm text-dark active">' 
            : '<li class="breadcrumb-item text-sm">';
        
        if (!$isActive) {
            $result .= '<a class="opacity-5 text-dark" href="' . $escapeHtml($link) . '">' . $escapeHtml($label) . '</a>';
        } else {
            $result .= $escapeHtml($label);
        }
                    
        $result .= '</li>';
    
        return $result;
    }
}

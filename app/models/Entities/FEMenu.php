<?php

namespace Models\Entities;

use \Models\Entities\Generated;
use \Doctrine\ORM\Mapping\Entity;
use \Doctrine\ORM\Mapping\Table;
use \Doctrine\ORM\Mapping\OrderBy;
use \Doctrine\ORM\Mapping\ManyToOne;
use \Doctrine\ORM\Mapping\JoinColumn;
use \Doctrine\ORM\Mapping\OneToMany;

/**
 * @Entity(repositoryClass="\Models\Repositories\FEMenu")
 * @Table(name="tbl_fe_menu")
 */
class FEMenu extends Generated\FEMenu
{
    const DOMAIN_CTM = 'CTM';
    const DOMAIN_BLOG = 'BLOG';

    const TYPE_LINK = 'LINK';
    const TYPE_TEXT = 'TEXT';
    const TYPE_IMAGE = 'IMAGE';
    const TYPE_IMAGE_LINK = 'IMAGE_LINK';
    const TYPE_REFER_LINK = 'REFER_LINK';
    const TYPE_INPUT = 'INPUT';
    const MAX_DISPLAY_ROOT_ITEM = 10;
    const MAX_DISPLAY_COLUMN = 4;
    const MAX_DISPLAY_FOOTER_COLUMN = 4;

    const POSITION_HEADER = 'HEADER';
    const POSITION_FOOTER = 'FOOTER';
    const POSITION_CTM_FAQ = 'CTM_FAQ';

    const DOMAIN_MAIN = 'KIETDECOR';
    const DOMAIN_LIFE = 'LIFE';


    public static function getTypes($position = self::POSITION_HEADER)
    {
        $types = [
            self::TYPE_TEXT => 'Text',
            self::TYPE_LINK => 'Link',
            self::TYPE_INPUT => 'Input',
            self::TYPE_REFER_LINK => 'Only Link',
        ];
        if ($position == self::POSITION_FOOTER) {
            $types = [
                self::TYPE_TEXT => 'Text',
                self::TYPE_LINK => 'Link',
                self::TYPE_IMAGE => 'Image',
                self::TYPE_IMAGE_LINK => 'Image Link',
                self::TYPE_REFER_LINK => 'Only Link',
            ];
        }
        return $types;
    }

    /**
     * Folder upload icon for menu item
     * @var string
     */
    const BASE_IMG_FOLDER = '/uploads/menu_icons';

    /**
     * @ManyToOne(targetEntity="\Models\Entities\FEMenu")
     * @JoinColumn(name="menu_parent_id", referencedColumnName="menu_id")
     */
    protected $parent;

    public function setParent(FEMenu $parent)
    {
        $this->parent = $parent;
        $this->menu_parent_id = $parent->menu_id ?? null;
        return $this;
    }

    /**
     * @OneToMany(targetEntity="\Models\Entities\FEMenu", mappedBy="parent")
     * @OrderBy({"menu_order" = "ASC", "menu_title" = "ASC"})
     */
    protected $childs;

    public function addChilds(FEMenu $child)
    {
        $this->childs->add($child);
        return $this;
    }

    /**
     * Get menu icon
     * @param string $icon
     * @return string
     */
    public static function getIcon(string $icon = '')
    {
        if ($icon)
            return self::BASE_IMG_FOLDER . "/{$icon}";
        return '';
    }

    /**
     * @param string $link
     * @return void
     */
    public function setMenu_link(string $link = '')
    {
        if (!empty($link) && strlen($link) > 0) {
            $this->menu_link = strpos($link, 'http') === 0 ? $link : strtolower($link);
        } else
            $this->menu_link = null;
        return $this;
    }

    /**
     * Get full image path
     * @return string
     */
    public function getMenuIcon()
    {
        return self::getIcon($this->menu_icon);
    }

    /**
     * @param array $opts
     * @return void
     */
    public function setMenu_style(array $opts = [])
    {
        $json = '{}';
        if ($opts)
            $json = json_encode($opts);
        $this->menu_style = $json;
        return $this;
    }

    /**
     * @param string $json
     * @param boolean $isJson
     * @return void
     */
    public static function decodeMenuStyle(string $json = '', bool $isJson = true)
    {
        if (true === $isJson)
            return $json;

        $rs = [];
        if ($json && $json != '{}')
            $rs = @json_decode($json, true);
        return $rs;
    }

    /**
     * Get decode of style
     *
     * @param boolean $isJson
     * @return void
     */
    public function getMenuStyle(bool $isJson = true)
    {
        return self::decodeMenuStyle($this->menu_style, $isJson);
    }
    const ARR_DATA_KEY = [
        'code',
        'title',
        'icon',
        'link',
        'style',
        'order',
        'status',
        'level',
        'is_login',
        'type'
    ];

    /**
     * @param [type] $scope
     * @return void
     */
    public function toArrayData(array $scope = self::ARR_DATA_KEY)
    {
        $json = [];
        foreach ($scope as $key) {
            $json[$key] = $this->__get("menu_" . $key);
        }

        return $json;
    }
}

?>
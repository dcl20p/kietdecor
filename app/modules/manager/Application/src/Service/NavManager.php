<?php
namespace Application\Service;

/**
 * This service is responsible for determining which items should be in the main menu.
 * The items may be different depending on whether the user is authenticated or not.
 */
class NavManager
{
    /**
     * @var Laminas\View\Helper\Url
     */
    private $urlHelper;

    /**
     * @var \stdClass|null
     */
    private $authen;

    /**
     * Constructs the service.
     */
    public function __construct($urlHelper, $authen = null)
    {
        $this->urlHelper = $urlHelper;
        $this->authen = $authen;
    }

    /**
     * This method returns menu items depending on whether user has logged in or not.
     */
    public function getMenuItems()
    {
        return require __DIR__ . '/../../config/NavItems.php';
    }
}
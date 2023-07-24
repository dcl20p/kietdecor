<?php 
namespace Manager\Controller;
use Laminas\View\Model\ViewModel;
use Zf\Ext\Controller\ZfController;

class FEMenuController extends ZfController
{
    /**
     * List Front end menu
     *
     * @return ViewModel
     */
    public function indexAction(): ViewModel
    {
        $routeName = $this->getCurrentRouteName();
        $parentId = $this->getParamsRoute('parent_id', null);
        try {
            if ($this->isPostRequest()) {

            }
        } catch (\Throwable $e) {
            $this->saveErrorLog($e);
            $this->addErrorMessage(
                $this->mvcTranslate(ZF_MSG_WENT_WRONG)
            );
        }

        return new ViewModel([
            'paginator' => $paginator ?? null,
            'pageTitle' => $this->mvcTranslate('Quản lý header'),
            'routeName' => $this->getCurrentRouteName(),
        ]);
    }
}
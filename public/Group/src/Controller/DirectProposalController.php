<?php
namespace Group\Controller;

use ArrayObject;
use Laminas\Http\PhpEnvironment\Response;
use Laminas\View\Model\ViewModel;
use Models\Entities\GroupArea;
use Zf\Ext\Controller\ZfController;
use Laminas\View\Model\JsonModel;

/**
 * @controllerTitle: Management sale
 */
class DirectProposalController extends ZfController
{
    /**
     * @return ViewModel
     */
    public function indexAction(): ViewModel
    {
        try {

        } catch (\Throwable $e) {
            $this->saveErrorLog($e);
        }
        return new ViewModel([
        'paginator'    => $paginator ?? null,
        'totalItem'    => $paginator?->getTotalItemCount() ?? 0,
        'pageTitle'    => $this->mvcTranslate('Sending group settings'),
        'routeName'    => $this->getCurrentRouteName(),
        'activeItemId' => 'direct_proposal'
        ]);
    }

    /**
     * @return ViewModel
     */
    public function addAction(): ViewModel
    {
        $postVals = [];
        try {
            if ($this->isPostRequest()) {
                $postVals = $this->paramsPost();
            }

        } catch (\Throwable $e) {
            $this->saveErrorLog($e);
            $this->addErrorMessage(
                $this->mvcTranslate(ZF_MSG_ADD_FAIL)
            );
        }

        return new ViewModel([
            'postVals' => $postVals ?? [],
            'pageTitle' => $this->mvcTranslate('Sending group new registration'),
            'routeName' => $this->getCurrentRouteName(),
            'activeItemId' => 'direct_proposal'
        ]);
    }

    /**
     * Edit
     *
     * @return ViewModel|Response
     */
    public function editAction(): ViewModel|Response
    {
        try {

        } catch (\Throwable $e) {
            $this->saveErrorLog($e);
            $this->addErrorMessage(
                $this->mvcTranslate(ZF_MSG_UPDATE_FAIL)
            );
        }

        return (new ViewModel([
            'postVals'      => $postVals ?? [],
            'pageTitle'     => $this->mvcTranslate('Sending group new registration'),
            'routeName'     => $this->getCurrentRouteName(),
            'activeItemId'  => 'direct_proposal',
            'isEdit'        => true
        ]))->setTemplate('group/direct-proposal/add.phtml');
    }
}
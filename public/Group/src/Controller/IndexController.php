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
class IndexController extends ZfController
{
    /**
     * Get parent by ID
     * @param integer $id
     * @return object|NULL
     */
    protected function getParentEntity($id)
    {
        if (
            empty($entity = $this->getEntityRepo(GroupArea::class)
                ->find($id))
        ) {
            $this->getResponse()->setStatusCode(404);
            return exit();
        }
        return $entity;
    }

    /**
     * @return ViewModel
     */
    public function indexAction(): ViewModel
    {
        try {
            $params = array_intersect_key(
                $this->paramsQuery(),
                [
                    'status' => 0,
                    'ids' => null,
                    'keyword' => ''
                ]
            );

            $parentId = intval($this->paramsRoute('pid', null));
            if (!empty($parentId)) {
                $parent = $this->getParentEntity($parentId);
                $params['parent_id'] = $parent->ga_id;
            } else {
                $parent = null;
                $params['only_parent'] = true;
            }

            $paginator = $this->getDoctrinePaginator(
                $this->getEntityRepo(GroupArea::class)
                    ->fetchOpts([
                        'params' => array_replace($params, [
                            'ctm_id' => $this->getAuthen()->root_id
                        ]),
                        'resultMode' => 'Query'
                    ]), false)
                ->setItemCountPerPage($this->paramsQuery('limit', 30))
                ->setCurrentPageNumber($this->paramsQuery('page', 1));

        } catch (\Throwable $e) {
            $this->saveErrorLog($e);
        }
        return (new ViewModel([
        'paginator'    => $paginator ?? null,
        'parentEntity' => $parent ?? null,
        'totalItem'    => $paginator?->getTotalItemCount() ?? 0,
        'currentItem'  => $paginator?->getCurrentItemCount() ?? 0,
        'pageTitle'    => $parent
                            ? $this->mvcTranslate('Prefecture category')
                            : $this->mvcTranslate('Regional category'),
        'routeName'    => $this->getCurrentRouteName(),
        'activeItemId' => 'group-area'
        ]))->setTemplate('group/index/index.phtml');
    }

    /**
     * @return ViewModel|Response
     */
    public function addAction(): ViewModel|Response
    {
        $parentId = $this->paramsRoute('pid', null);
        if (!empty($parentId))
            $parent = $this->getParentEntity($parentId);
        else
            $parent = null;

        $postVals = [];
        try {
            if ($this->isPostRequest()) {
                $postVals = $this->paramsPost();

                foreach (['title', 'name'] as $value) {
                    $postVals[$value] = trim(
                        mb_substr($postVals[$value], 0, 15)
                    );
                }

                if ('' !== $postVals['title'] && '' !== $postVals['name']) {
                    $repo = $this->getEntityRepo(GroupArea::class);
                    $insertData = [
                        'code'      => $this->getZfHelper()->getRandomCode([
                            'id'     => time(),
                            'maxLen' => 19
                        ]),
                        'title'     => $title = $postVals['title'],
                        'name'      => $name = $postVals['name'],
                        'status'    => (int) $postVals['status'],
                        'time'      => time(),
                        'ctm_id'    => $this->getAuthen()->root_id,
                        'ctm_code'  => $this->getAuthen()->ctm_root_code,
                        'keyword'   => $repo->makeFtSearch($title, $name)
                    ];

                    if (!empty($parent)) $insertData['parent_id'] = $parentId;
                    
                    $repo->createGroupArea($insertData);

                    $this->addSuccessMessage(
                        $this->mvcTranslate(ZF_MSG_ADD_SUCCESS)
                    );

                    return $this->zfRedirect()->toCurrentRoute([
                        'action' => empty($parentId) ? null : 'small',
                        'pid' => $parentId,
                    ], ['useOldQuery' => true]);
                } else {
                    $this->addErrorMessage(
                        $this->mvcTranslate(ZF_MSG_NOT_EMPTY)
                    );
                }
            }

        } catch (\Throwable $e) {
            $this->saveErrorLog($e);
            $this->addErrorMessage(
                $this->mvcTranslate(ZF_MSG_ADD_FAIL)
            );
        }

        return new ViewModel([
            'parentEntity' => $parent ?? null,
            'postVals'     => $postVals ?? [],
            'pageTitle'    => $parent 
                                ? $this->mvcTranslate('Create new prefecture category')
                                : $this->mvcTranslate('Create new area category'),
            'routeName'    => $this->getCurrentRouteName(),
            'activeItemId' => 'group-area'
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
            $repo = $this->getEntityRepo(GroupArea::class);
            $parentId = $this->paramsRoute('pid', null);
            if (!empty($parentId))
                $parent = $this->getParentEntity($parentId);
            else $parent = null;

            if (empty($id = $this->paramsRoute('id', null))
                || empty($entity = $repo->findOneBy(['ga_id' => $id]))
            ) { 
                $this->getResponse()->setStatusCode(404);
                return new ViewModel([]);
            }

            if ($this->isPostRequest()) {
                $postVals = $this->paramsPost();
                foreach (['title', 'name'] as $value) {
                    $postVals[$value] = trim(
                        mb_substr($postVals[$value], 0, 15)
                    );
                }

                if ('' !== $postVals['title'] && '' !== $postVals['name']) {
                    $repo->updateGroupArea([
                        'id'        => $entity->ga_id,
                        'title'     => $title = $postVals['title'],
                        'name'      => $name  = $postVals['name'],
                        'status'    => (int) $postVals['status'],
                        'edit_time' => time(),
                        'keyword'   => $repo->makeFtSearch($title, $name)
                    ]);

                    $this->addSuccessMessage(
                        $this->mvcTranslate(ZF_MSG_UPDATE_SUCCESS)
                    );

                    return $this->zfRedirect()->toCurrentRoute([
                        'action' => empty($parentId) ? null : 'small',
                        'pid'    => $parentId,
                    ], ['useOldQuery' => true]);

                } else {
                    $this->addErrorMessage(
                        $this->mvcTranslate(ZF_MSG_NOT_EMPTY)
                    );
                }
            } else $postVals = [
                'title'  => $entity->ga_title,
                'name'   => $entity->ga_name,
                'status' => $entity->ga_status,
            ];

        } catch (\Throwable $e) {
            $this->saveErrorLog($e);
            $this->addErrorMessage(
                $this->mvcTranslate(ZF_MSG_UPDATE_FAIL)
            );
        }

        return (new ViewModel([
            'parentEntity'  => $parent ?? null,
            'postVals'      => $postVals ?? [],
            'pageTitle'     => $parent 
                                ? $this->mvcTranslate('Edit prefecture category')
                                : $this->mvcTranslate('Edit area category'),
            'routeName'     => $this->getCurrentRouteName(),
            'activeItemId'  => 'group-area',
            'isEdit'        => true
        ]))->setTemplate('group/index/add.phtml');
    }

    /**
     * @return Response
     */
    public function changeOrderAction(): Response
    {
        if ($this->isPostRequest()) {
            $this->_saveOrder(
                $this->paramsPost('order', [])
            );
        }

        $parentId = $this->paramsRoute('pid', null);
        // End
        return $this->zfRedirect()->toRoute(
            null,
            [
                'action' => empty($parentId) ? null : 'small',
                'pid' => $parentId
            ],
            ['useOldQuery' => true]
        );
    }

    /**
     * @param array $orders
     */
    protected function _saveOrder(array $orders = []): void
    {
        try {
            $needRollback = false;
            $ids = array_keys($orders);

            $entities = $this->getEntityRepo(GroupArea::class)
                ->findBy(['ga_id' => $ids]);

            $time = time();
            foreach ($entities as $idx => $entity) {
                $entities[$idx]->ga_order = $orders[$entity->ga_id];
                $entities[$idx]->ga_edit_time = $time;
            }

            $this->getEntityManager()->beginTransaction();
            $needRollback = true;
            $this->getEntityManager()->flush($entities);
            $this->getEntityManager()->commit();
            $needRollback = false;

            $this->addSuccessMessage(
                $this->mvcTranslate(ZF_MSG_SAVE_ORDER_SUCCESS)
            );

        } catch (\Throwable $e) {
            if ($needRollback)
                $this->getEntityManager()->rollback();

            $this->saveErrorLog($e);
            $this->addErrorMessage(
                $this->mvcTranslate('Save order fail.')
            );
        }
    }

    /**
     * Change status
     *
     * @return JsonModel
     */
    public function changeStatusAction(): JsonModel
    {
        try {
            $parentId = $this->paramsRoute('pid', null);
            if (!empty($parentId))
                $parent = $this->getParentEntity($parentId);
            else
                $parent = null;

            $id = (int) $this->paramsRoute('id', 0);
            $status = $this->paramsPost('status', null);
            $validState = ['0' => 0, '1' => 1];
            $repo = $this->getEntityRepo(GroupArea::class);
            if (
                !isset($validState[$status])
                || $id < 1
                || empty($entity = $repo->find($id))
            ) {
                return new JsonModel(['status' => false]);
            }

            $repo->changeStatus([
                'status' => $status = ((empty($parent) || $parent->ga_status > 0)
                    ? $status : 0),

                'id' => $entity->ga_id
            ]);

            return new JsonModel(['status' => $status]);
        } catch (\Throwable $e) {
            $this->saveErrorLog($e);
            return new JsonModel(['status' => false]);
        }
    }

    /**
     * List small
     *
     * @return ViewModel
     */
    public function smallAction(): ViewModel
    {
        return $this->indexAction();
    }
}
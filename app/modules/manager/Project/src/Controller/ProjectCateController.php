<?php

declare(strict_types=1);

namespace Project\Controller;

use ArrayObject;
use Laminas\Http\PhpEnvironment\Response;
use Models\Entities\Project;
use Models\Entities\ProjectCate;
use \Zf\Ext\Controller\ZfController;
use Laminas\View\Model\ViewModel;
use Laminas\View\Model\JsonModel;

class ProjectCateController extends ZfController
{
    /**
     * List project cat action
     *
     * @return ViewModel
     */
    public function indexAction(): ViewModel
    {
        try {
            $repo = $this->getEntityRepo(ProjectCate::class);

            $parentId = intval($this->getParamsRoute('pid', null));
            if (!empty($parentId)) {
                $parent = $this->getParentEntity($parentId, $repo);
                $params['parent_id'] = $parent->prc_id;
            } else {
                $parent = null;
                $params['only_parent'] = true;
            }

            $limit = (int) $this->getParamsQuery('limit', 30);
            $page = (int) $this->getParamsQuery('page', 1);

            $paginator = $this->getZfPaginator(
                $repo->fetchOpts([
                    'params' => $params,
                    'resultMode' => 'Query'
                ]), $limit, $page
            );
            
        } catch (\Throwable $e) {
            $this->saveErrorLog($e);
        }

        return (new ViewModel([
            'paginator'     => $paginator ?? null,
            'parentEntity'  => $parent ?? null,
            'routeName'     => $this->getCurrentRouteName(),
            'pageTitle'     => $this->mvcTranslate('Loại dự án'),
            'activeItemId'  => 'project_cate'
        ]))->setTemplate('project/project-cate/index.phtml');
    }

    /**
     * Validate param post project cate
     *
     * @return array|false
     */
    protected function validDataProjectCate($params): array|false
    {
        $params = array_intersect_key($params, [
            'name'         => '',
            'image'        => '',
            'alias'        => '',
            'status'       => 'off',
            'is_use'       => 'off',
            'meta_title'   => '',
            'meta_keyword' => '',
        ]);

        $params['name']  = trim(mb_substr($params['name'], 0, 100));
        $params['alias'] = trim(mb_substr($params['alias'], 0, 500));
        $params['image'] = trim(mb_substr($params['image'], 0, 100));
        $params['meta_title'] = trim(mb_substr($params['meta_title'], 0, 1024));
        $params['meta_keyword'] = trim(mb_substr($params['meta_keyword'], 0, 2048));
        $params['status'] = isset($params['status']) && $params['status'] == 'on' ? 1 : 0;
        $params['is_use'] = isset($params['is_use']) && $params['is_use'] == 'on' ? 1 : 0;

        if ($params['name'] == '') {
            $this->addErrorMessage(
                $this->mvcTranslate(ZF_MSG_REQUIRE_DATA)
            );
            return false;
        }

        return $params;
    }

    /**
     * Get parent by ID
     *
     * @param integer $id
     * @param mixed $repo
     * @return mixed
     */
    protected function getParentEntity(int $id, $repo): mixed
    {
        if (empty($entity = $repo->find($id))) {
            $this->getResponse()->setStatusCode(404);
            return exit();
        }
        return $entity;
    }

    /**
     * Add project cate action
     *
     * @return ViewModel|Response
     */
    public function addAction(): ViewModel|Response
    {
        try {
            $repo = $this->getEntityRepo(ProjectCate::class);
            $parentId = (int) $this->getParamsRoute('pid', null);
            if (!empty($parentId))
                $parent = $this->getParentEntity($parentId, $repo);
            else
                $parent = null;

            $postData = [];
            if ($this->isPostRequest()) {
                if ($dataPost = $this->validDataProjectCate($this->getParamsPost())) {
                    $params = [];
                    foreach ($dataPost as $key => $item) {
                        $params["prc_{$key}"] = $item;
                    }

                    $params = array_replace($params, [
                        'prc_code'         => $this->getZfHelper()->getRandomCode([
                                                'id' => time(), 'maxLen' => 19
                                            ]),
                        'prc_created_by'   => $this->getAuthen()->adm_id,
                        'prc_created_time' => time(),
                        'prc_edit_by'      => $parentId
                    ]);

                    if (!empty($parentId)) $params['prc_parent_id'] = $parentId;

                    $repo->insertData($params);

                    $this->addSuccessMessage(
                        $this->mvcTranslate(ZF_MSG_ADD_SUCCESS)
                    );

                    return $this->zfRedirect()->toCurrentRoute([
                        'action' => empty($parentId) ? null : 'small',
                        'pid' => $parentId,
                    ], ['useOldQuery' => true]);
                }
            }
        } catch (\Throwable $e) {
            $this->saveErrorLog($e);
            $this->addErrorMessage(
                $this->mvcTranslate(ZF_MSG_WENT_WRONG)
            );
        }
        return new ViewModel([
            'postData'      => $postData ?? [],
            'pageTitle'     => $parent 
                                ? $this->mvcTranslate('Thêm loại dự án (Danh mục con)')
                                : $this->mvcTranslate('Thêm loại dự án'),
            'routeName'     => $this->getCurrentRouteName(),
            'activeItemId'  => 'project_cate'
        ]);
    }


    /**
     * Edit project cate action
     *
     * @return ViewModel|Response
     */
    public function editAction(): ViewModel|Response
    {
        try {
            $repo = $this->getEntityRepo(ProjectCate::class);

            $parentId = (int) $this->getParamsRoute('pid', null);
            if (!empty($parentId))
                $parent = $this->getParentEntity($parentId, $repo);
            else $parent = null;

            if (empty($id = $this->getParamsRoute('id', null))
                || empty($entity = $repo->findOneBy(['prwwc_id' => $id]))
            ) { 
                $this->getResponse()->setStatusCode(404);www
                return new ViewModel([]);
            }

            $postData = [];
            if ($this->isPostRequest()) {
                if ($postData = $this->validDataProjectCate($this->getParamsPost())) {
                    $params = [];
                    foreach ($postData as $key => $item) {
                        $params["prc_{$key}"] = $item;
                    }

                    $params = array_replace($params, [
                        'prc_updated_time' => time(),
                        'prc_edit_by'      => $this->getAuthen()->adm_id,
                    ]);

                    $repo->updateData($entity, $params);

                    $this->addSuccessMessage(
                        $this->mvcTranslate(ZF_MSG_ADD_SUCCESS)
                    );

                    return $this->zfRedirect()->toCurrentRoute([
                        'action' => empty($parentId) ? null : 'small',
                        'pid' => $parentId,
                    ], ['useOldQuery' => true]);
                }
            } else $postData = [
                'name'         => $entity->prc_name,
                'alias'        => $entity->prc_alias,
                'status'       => $entity->prc_status,
                'is_use'       => $entity->prc_is_use,
                'meta_title'   => $entity->prc_meta_title,
                'meta_keyword' => $entity->prc_meta_keyword,
                'meta_desc'    => $entity->prc_meta_desc,
                'image'        => $entity->prc_image,
            ];
        } catch (\Throwable $e) {
            $this->saveErrorLog($e);
            $this->addErrorMessage(
                $this->mvcTranslate(ZF_MSG_UPDATE_FAIL)
            );
        }

        return (new ViewModel([
            'parentEntity'  => $parent ?? null,
            'postData'      => $postData ?? [],
            'pageTitle'     => $parent 
                                ? $this->mvcTranslate('Sửa loại dự án (Danh mục con)')
                                : $this->mvcTranslate('Sửa loại dự án'),
            'routeName'     => $this->getCurrentRouteName(),
            'activeItemId'  => 'group-skill',
            'isEdit'        => true
        ]))->setTemplate('project/project-cate/add.phtml');
    }

    /**
     * Change status
     *
     * @return JsonModel
     */
    public function changeStatusAction(): JsonModel
    {
        try {
            $repo = $this->getEntityRepo(ProjectCate::class);
            $parentId = (int) $this->getParamsRoute('pid', null);
            if (!empty($parentId))
                $parent = $this->getParentEntity($parentId, $repo);
            else
                $parent = null;

            $id = (int) $this->getParamsRoute('id', 0);
            $status = (int) $this->getParamsPayload('status', null);

            if (!$this->isValidCsrfToken(
                null, ProjectCate::FOLDER_TOKEN
            )) {
                return $this->returnJsonModel(
                    false, 'went_wrong', ProjectCate::FOLDER_TOKEN
                );
            }

            if (
                !in_array($status, [0, 1])
                || $id < 1
                || empty($entity = $repo->find($id))
            ) {
                return new JsonModel(['status' => false]);
            }

            if ($status != $entity->prc_status) {
                $repo->changeStatus([
                    'status' => $status = ((empty($parent) || $parent->prc_status > 0)
                        ? $status : 0),
                    'id' => $entity->prc_id
                ]);
            }
            $this->addSuccessMessage(
                $this->mvcTranslate(ZF_MSG_UPDATE_SUCCESS)
            );
            return $this->returnJsonModel(
                true, 'update', ProjectCate::FOLDER_TOKEN
            );
        } catch (\Throwable $e) {
            $this->saveErrorLog($e);
            $this->addErrorMessage(
                $this->mvcTranslate(ZF_MSG_UPDATE_FAIL)
            );
        }
        
        return $this->returnJsonModel(
            false, 'update', ProjectCate::FOLDER_TOKEN
        );
    }

    /**
     * Delete action
     *
     * @return Response
     */
    public function deleteAction(): Response
    {
        $routeName = $this->getCurrentRouteName();
        try {
            if ($this->isPostRequest()) {
                $ids = $this->getParamsPost('id', []);
                $repo = $this->getEntityRepo(ProjectCate::class);

                if (!$ids
                    || empty($entities = $repo->findBy(['prc_id' => $ids]))
                ) {
                    $this->addErrorMessage(
                        $this->mvcTranslate(ZF_MSG_DATA_NOT_EXISTS)
                    );
                    return $this->redirectToRoute($routeName, [], ['useOldQuery' => true]);
                }
                $repo->deleteData($ids);
                $this->addSuccessMessage(
                    $this->mvcTranslate(ZF_MSG_DEL_SUCCESS)
                );
            } else {
                $this->addErrorMessage(
                    $this->mvcTranslate(ZF_MSG_NOT_ALLOW)
                );
            }
        } catch (\Throwable $e) {
            $this->saveErrorLog($e);
            $this->addSuccessMessage(
                $this->mvcTranslate(ZF_MSG_DEL_FAIL)
            );
        }
        return $this->redirectToRoute($routeName, [], ['useOldQuery' => true]);
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

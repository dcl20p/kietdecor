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

class IndexController extends ZfController
{
    /**
     * List action
     *
     * @return ViewModel
     */
    public function indexAction(): ViewModel
    {
        try {
            $repo = $this->getEntityRepo(Project::class);

            $params = [
                'params' => [],
                'resultMode' => 'Query'
            ];

            $limit = (int) $this->getParamsQuery('limit', 30);
            $page = (int) $this->getParamsQuery('page', 1);

            $paginator = $this->getZfPaginator(
                $repo>fetchOpts($params),
                $limit,
                $page
            );
            
        } catch (\Throwable $e) {
            $this->saveErrorLog($e);
        }

        return new ViewModel([
            'paginator' => $paginator ?? new ArrayObject(),
            'routeName' => $this->getCurrentRouteName(),
            'pageTitle' => $this->mvcTranslate('Danh sách dự án'),
            'activeItemId'  => 'project'
        ]);
    }

    public function addAction()
    {
        return new ViewModel([
            'routeName'     => $this->getCurrentRouteName(),
            'pageTitle'     => $this->mvcTranslate('Thêm dự án'),
            'activeItemId'  => 'project'
        ]);
    }

    public function deleteAction()
    {
        $this->addSuccessMessage('Xoá thành công');
        return $this->zfRedirect()->toCurrentRoute(
            [], ['useOldQuery' => true]
        );
    }

    /**
     * List project cat action
     *
     * @return ViewModel
     */
    public function listCateAction(): ViewModel
    {
        try {
            $repo = $this->getEntityRepo(ProjectCate::class);

            $params = [
                'params' => [],
                'resultMode' => 'Query'
            ];

            $limit = (int) $this->getParamsQuery('limit', 30);
            $page = (int) $this->getParamsQuery('page', 1);

            $paginator = $this->getZfPaginator(
                $repo>fetchOpts($params),
                $limit,
                $page
            );
            
        } catch (\Throwable $e) {
            $this->saveErrorLog($e);
        }

        return new ViewModel([
            'paginator' => $paginator ?? null,
            'routeName' => $this->getCurrentRouteName(),
            'pageTitle' => $this->mvcTranslate('Loại dự án'),
            'activeItemId'  => 'project_cate'
        ]);
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
            'status'       => 'off',
            'is_use'       => 'off',
            'meta_title'   => '',
            'meta_desc'    => '',
            'meta_keyword' => '',
        ]);

        $params['name']  = trim(mb_substr($params['title'], 0, 100));
        $params['image'] = trim(mb_substr($params['image'], 0, 100));
        $params['meta_title'] = trim(mb_substr($params['meta_title'], 0, 1024));
        $params['meta_keyword'] = trim(mb_substr($params['description'], 0, 2048));
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
        if (
            empty($entity = $this->getEntityRepo($repo)
                ->find($id))
        ) {
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
    public function addCateAction(): ViewModel|Response
    {
        try {
            $repo = $this->getEntityRepo(ProjectCate::class);
            $parentId = $this->paramsRoute('pid', null);
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
                        'prc_code' => $this->getZfHelper()->getRandomCode([
                                        'id' => time(), 'maxLen' => 19
                                    ]),
                        'prc_create_by'    => $this->getAuthen()->adm_id,
                        'prc_created_time' => time(),
                    ]);

                    if (!empty($parent)) $params['prc_parent_id'] = $parentId;

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
            'pageTitle'     => $this->mvcTranslate('Thêm loại dự án'),
            'routeName'     => $this->getCurrentRouteName(),
            'activeItemId'  => 'service'
        ]);
    }
}

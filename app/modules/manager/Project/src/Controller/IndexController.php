<?php

declare(strict_types=1);

namespace Project\Controller;

use ArrayObject;
use Laminas\Http\PhpEnvironment\Response;
use Models\Entities\Project;
use Models\Entities\ProjectCate; 
use Models\Entities\Service;
use Zf\Ext\Controller\ZfController;
use Laminas\View\Model\ViewModel;
use Laminas\View\Model\JsonModel;

class IndexController extends ZfController
{
    use \GeneralTraits\Controller\General;
    use \ImageTraits\Controller\UploadImages;
    /**
     * List action
     *
     * @return ViewModel
     */
    public function indexAction(): ViewModel
    {
        try {
            $repo        = $this->getEntityRepo(Project::class);
            $repoCate    = $this->getEntityRepo(ProjectCate::class);
            $repoService = $this->getEntityRepo(Service::class);

            $getData = array_filter(
                array_intersect_key($this->getParamsQuery(), [
                    'keyword' => '',
                    'prc_id'  => null,
                    'sv_id'  => null,
                ]
            ));

            $params = [
                'params' => $getData,
                'resultMode' => 'Query'
            ];

            $limit = (int) $this->getParamsQuery('limit', 30);
            $page = (int) $this->getParamsQuery('page', 1);

            $paginator = $this->getZfPaginator(
                $repo->fetchOpts($params),
                $limit,
                $page
            );

            $cates = $repoCate->getDataFromCache([
                'params' => [
                    'status'      => 1,
                    'only_parent' => true
                ]
            ]);

            $prCates = array_map(function ($item) {
                return $item['prc_name'];
            }, $cates);

            $services = $repoService->getDataFromCache([
                'params' => [
                    'status' => 1
                ]
            ]);

            $prServices = array_map(function ($item) {
                return $item['sv_title'];
            }, $services);

        } catch (\Throwable $e) {
            $this->addErrorMessage(
                $this->mvcTranslate(ZF_MSG_WENT_WRONG)
            );
            $this->saveErrorLog($e);
        }

        return new ViewModel([
            'paginator' => $paginator ?? null,
            'routeName' => $this->getCurrentRouteName(),
            'pageTitle' => $this->mvcTranslate('Danh sách dự án'),
            'activeItemId' => 'project',
            'prCates'      => $prCates ?? [],
            'prServices'   => $prServices ?? [],
            'getData'      => $getData
        ]);
    }

    /**
     * Validate param post
     *
     * @param array $params
     * @return array|false
     */
    protected function validData(array $params): array|false
    {
        $params = array_intersect_key($params, [
            'name'         => '',
            'location'     => '',
            'assigned_to'  => '',
            'status'       => 'off',
            'prc_id'       => null,
            'sv_id'        => null,
            'meta_title'   => '',
            'meta_keyword' => '',
            'description'  => '',
            'meta_desc'    => '',
            'thumbnail'    => '',
            'json_image'   => '',
        ]);
        $params['name']         = trim(mb_substr($params['name'], 0, 512));
        $params['location']     = trim(mb_substr($params['location'], 0, 512));
        $params['assigned_to']  = trim(mb_substr($params['assigned_to'], 0, 100));
        $params['description']  = $this->escapeString(mb_substr($params['description'], 0, 2048));
        $params['meta_desc']    = $this->escapeString(mb_substr($params['meta_desc'], 0, 10000));
        $params['meta_title']   = trim(mb_substr($params['meta_title'], 0, 1024));
        $params['meta_keyword'] = trim(mb_substr($params['meta_keyword'], 0, 2048));
        $params['status']       = isset($params['status']) && $params['status'] == 'on' ? 1 : 0;

        foreach (['sv_id', 'prc_id'] as $item) {
            $params[$item] = intval($params[$item]);
        }

        foreach (['name', 'sv_id', 'prc_id', 'thumbnail', 'json_image'] as $item) {
            if (empty($params[$item])) {
                $this->addErrorMessage(
                    $this->mvcTranslate(ZF_MSG_REQUIRE_DATA)
                );

                if ($item == 'thumbnail' || $item == 'json_image') {
                    $folderImage = $item == 'thumbnail' 
                        ? Project::PROJECT_THUMBNAIL_SIZES 
                        : Project::PROJECT_LIST_IMAGE_SIZES;

                    $this->revertUploadImageDropzone(
                        [$params['json_image'], $params['thumbnail']], 
                        FOLDER_IMAGE_PRODUCT, 
                        $folderImage
                    );
                }
                return false;
            }
        }

        return $params;
    }

    /**
     * Add action
     *
     * @return ViewModel|Response
     */
    public function addAction(): ViewModel|Response
    {
        try {
            $routeName   = $this->getCurrentRouteName();
            $repo        = $this->getEntityRepo(Project::class);
            $repoCate    = $this->getEntityRepo(ProjectCate::class);
            $repoService = $this->getEntityRepo(Service::class);

            $services = $repoService->getDataFromCache([
                'params' => [
                    'status' => 1
                ]
            ]);

            $cates = $repoCate->getDataFromCache([
                'params' => [
                    'status'      => 1,
                    'only_parent' => true
                ]
            ]);

            $code = $this->getZfHelper()->getRandomCode([
                'id' => time(), 'maxLen' => 19
            ]);

            $postData = [];
            if ($this->isPostRequest()) {
                if ($dataValid = $this->validData($this->getParamsPost())) {
                    if (empty($services[$dataValid['sv_id']])
                        || empty($cates[$dataValid['prc_id']])
                    ) {
                        $this->addErrorMessage(
                            $this->mvcTranslate(ZF_MSG_DATA_NOT_EXISTS)
                        );
                        return $this->redirectToRoute($routeName, [], ['useOldQuery' => true]);
                    }
                    $params = [];
                    foreach ($dataValid as $key => $item) {
                        $params["pr_{$key}"] = $item;
                    }

                    $repo->insertData(array_replace($params, [
                        'pr_sv_code'    => $services[$dataValid['sv_id']]['sv_code'],
                        'pr_prc_code'   => $cates[$dataValid['prc_id']]['prc_code'],
                        'pr_create_by'  => $this->getAuthen()->adm_id,
                        'pr_create_time'=> time(),
                        'pr_code'       => $code
                    ]));
                    $this->addSuccessMessage(
                        $this->mvcTranslate(ZF_MSG_ADD_SUCCESS)
                    );
    
                    return $this->zfRedirect()->toCurrentRoute([], ['useOldQuery' => true]);
                } else $postData = $this->getParamsPost();

            }

            $prCates = array_map(function ($item) {
                return $item['prc_name'];
            }, $cates);

            $prServices = array_map(function ($item) {
                return $item['sv_title'];
            }, $services);

        } catch (\Throwable $e) {
            $this->saveErrorLog($e);
            $this->addErrorMessage(
                $this->mvcTranslate(ZF_MSG_WENT_WRONG)
            );
        }
        return new ViewModel([
            'postData'     => $postData ?? [],
            'prCates'      => $prCates ?? [],
            'prServices'   => $prServices ?? [],
            'uid'          => $code ?? '',
            'routeName'    => $this->getCurrentRouteName(),
            'pageTitle'    => $this->mvcTranslate('Thêm dự án'),
            'activeItemId' => 'project',
            'isEdit'       => 0
        ]);
    }

    /**
     * Edit project action
     *
     * @return ViewModel|Response
     */
    public function editAction(): ViewModel|Response
    {
        try {
            $repo = $this->getEntityRepo(Project::class);
            $repoCate    = $this->getEntityRepo(ProjectCate::class);
            $repoService = $this->getEntityRepo(Service::class);

            if (empty($id = $this->getParamsRoute('id', null))
                || empty($entity = $repo->findOneBy(['pr_id' => $id]))
            ) { 
                $this->getResponse()->setStatusCode(404);
                return new ViewModel([]);
            }

            $services = $repoService->getDataFromCache([
                'params' => [
                    'status' => 1
                ]
            ]);

            $cates = $repoCate->getDataFromCache([
                'params' => [
                    'status'      => 1,
                    'only_parent' => true
                ]
            ]);
            $postData = [];
            if ($this->isPostRequest()) {
                if ($postData = $this->validData($this->getParamsPost())) {
                    $params = [];
                    foreach ($postData as $key => $item) {
                        $params["pr_{$key}"] = $item;
                    }

                    $params = array_replace($params, [
                        'pr_update_time' => time(),
                        'pr_edit_by'     => $this->getAuthen()->adm_id,
                    ]);

                    $repo->updateData($entity, $params);

                    $this->addSuccessMessage(
                        $this->mvcTranslate(ZF_MSG_ADD_SUCCESS)
                    );

                    return $this->zfRedirect()->toCurrentRoute([], ['useOldQuery' => true]);

                }
            } else $postData = [
                'name'         => $entity->pr_name,
                'location'     => $entity->pr_location,
                'assigned_to'  => $entity->pr_assigned_to,
                'prc_id'       => $entity->pr_prc_id,
                'sv_id'        => $entity->pr_sv_id,
                'description'  => $entity->pr_description,
                'thumbnail'    => $entity->pr_thumbnail,
                'json_image'   => $entity->pr_json_image,
                'status'       => $entity->pr_status,
                'meta_title'   => $entity->pr_meta_title,
                'meta_keyword' => $entity->pr_meta_keyword,
                'meta_desc'    => $entity->pr_meta_desc,
            ];

            $prCates = array_map(function ($item) {
                return $item['prc_name'];
            }, $cates);

            $prServices = array_map(function ($item) {
                return $item['sv_title'];
            }, $services);

        } catch (\Throwable $e) {
            $this->saveErrorLog($e);
            $this->addErrorMessage(
                $this->mvcTranslate(ZF_MSG_UPDATE_FAIL)
            );
        }

        return (new ViewModel([
            'postData'      => $postData ?? [],
            'pageTitle'     => $this->mvcTranslate('Sửa dự án'),
            'routeName'     => $this->getCurrentRouteName(),
            'activeItemId'  => 'project',
            'isEdit'        => 1,
            'prCates'       => $prCates ?? [],
            'prServices'    => $prServices ?? [],
            'uid'           => $entity->pr_code ?? ''
        ]))->setTemplate('project/index/add.phtml');
    }

    /**
     * Change status
     *
     * @return JsonModel
     */
    public function changeStatusAction(): JsonModel
    {
        try {
            $repo = $this->getEntityRepo(Project::class);

            $id = (int) $this->getParamsRoute('id', 0);
            $status = (int) $this->getParamsPayload('status', null);

            if (!$this->isValidCsrfToken(
                null, Project::FOLDER_TOKEN
            )) {
                return $this->returnJsonModel(
                    false, 'went_wrong', Project::FOLDER_TOKEN
                );
            }

            if (
                !in_array($status, [0, 1])
                || $id < 1
                || empty($entity = $repo->find($id))
            ) {
                return new JsonModel(['status' => false]);
            }

            if ($status != $entity->pr_status) {
                $repo->updateData($entity, [
                    'pr_status' => $status,
                ]);
            }
            $this->addSuccessMessage(
                $this->mvcTranslate(ZF_MSG_UPDATE_SUCCESS)
            );
            return $this->returnJsonModel(
                true, 'update', Project::FOLDER_TOKEN
            );
        } catch (\Throwable $e) {
            $this->saveErrorLog($e);
            $this->addErrorMessage(
                $this->mvcTranslate(ZF_MSG_UPDATE_FAIL)
            );
        }
        
        return $this->returnJsonModel(
            false, 'update', Project::FOLDER_TOKEN
        );
    }

    /**
     * Delete project
     *
     * @return Response
     */
    public function deleteAction(): Response
    {
        $routeName = $this->getCurrentRouteName();
        try {
            if ($this->isPostRequest()) {
                $ids = $this->getParamsPost('id', []);
                $repo = $this->getEntityRepo(Project::class);

                if (!$ids
                    || empty($entities = $repo->findBy(['pr_id' => $ids]))
                ) {
                    $this->addErrorMessage(
                        $this->mvcTranslate(ZF_MSG_DATA_NOT_EXISTS)
                    );
                    return $this->redirectToRoute($routeName, [], ['useOldQuery' => true]);
                }
                foreach ($entities as $entity) {
                    $this->getEntityManager()->remove($entity);
                }
                $this->flushTransaction();
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
}
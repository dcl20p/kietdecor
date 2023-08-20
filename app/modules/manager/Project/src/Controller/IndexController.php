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
            $repo = $this->getEntityRepo(Project::class);

            $params = [
                'params' => [],
                'resultMode' => 'Query'
            ];

            $limit = (int) $this->getParamsQuery('limit', 30);
            $page = (int) $this->getParamsQuery('page', 1);

            $paginator = $this->getZfPaginator(
                $repo->fetchOpts($params),
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
            'activeItemId' => 'project'
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
                $strImage = $params['json_image'] . 
                    (!empty($params['thumbnail']) ? ',' . $params['thumbnail'] : '');
                foreach (explode(',', $strImage) as $fileName) {
                    $this->revertUploadImageDropzone(
                        $fileName, 
                        FOLDER_IMAGE_PRODUCT, 
                        Project::PROJECT_IMAGE_SIZES
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

            $cates = $repoCate->fetchOpts([
                'params' => [
                    'status'      => 1,
                    'only_parent' => true
                ],
                'order'      => ['name' => 'ASC'],
                'resultMode' => 'QueryBuilder'
            ])
                ->select('partial PRC.{prc_id,prc_name,prc_code}')
                ->indexBy('PRC', 'PRC.prc_id')
                ->getQuery()->getArrayResult() ?? [];

            $prCates = array_map(function ($item) {
                return $item['prc_name'];
            }, $cates);

            $services = $repoService->fetchOpts([
                'params' => [
                    'status' => 1
                ],
                'order'      => ['title' => 'ASC'],
                'resultMode' => 'QueryBuilder'
            ])
                ->select('partial SV.{sv_id,sv_title,sv_code}')
                ->indexBy('SV', 'SV.sv_id')
                ->getQuery()->getArrayResult() ?? [];

            $prServices = array_map(function ($item) {
                return $item['sv_title'];
            }, $services);

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
                        'pr_code' => $this->getZfHelper()->getRandomCode([
                                        'id' => time(), 'maxLen' => 19
                                    ]),
                        'pr_sv_code'    => $services[$dataValid['sv_id']]['sv_code'],
                        'pr_prc_code'   => $cates[$dataValid['prc_id']]['prc_code'],
                        'pr_create_by'  => $this->getAuthen()->adm_id,
                        'pr_create_time'=> time(),
                    ]));
                    $this->addSuccessMessage(
                        $this->mvcTranslate(ZF_MSG_ADD_SUCCESS)
                    );
    
                    return $this->zfRedirect()->toCurrentRoute([], ['useOldQuery' => true]);
                } else $postData = $this->getParamsPost();
            }

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
            'routeName'    => $this->getCurrentRouteName(),
            'pageTitle'    => $this->mvcTranslate('Thêm dự án'),
            'activeItemId' => 'project'
        ]);
    }

    public function deleteAction()
    {
        $this->addSuccessMessage('Xoá thành công');
        return $this->zfRedirect()->toCurrentRoute(
            [],
            ['useOldQuery' => true]
        );
    }
}
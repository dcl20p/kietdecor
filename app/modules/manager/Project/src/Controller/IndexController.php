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

    public function addAction()
    {
        try {
            $repo        = $this->getEntityRepo(Project::class);
            $repoCate    = $this->getEntityRepo(ProjectCate::class);
            $repoService = $this->getEntityRepo(Service::class);

            $cates = $repoCate->fetchOpts([
                'params' => [
                    'only_parent' => true
                ],
                'order'      => ['name' => 'ASC'],
                'resultMode' => 'QueryBuilder'
            ])
                ->select('partial PRC.{prc_id,prc_name}')
                ->indexBy('PRC', 'PRC.prc_id')
                ->getQuery()->getArrayResult() ?? [];

            $prCates = array_map(function ($item) {
                return $item['prc_name'];
            }, $cates);

            $services = $repoService->fetchOpts([
                'params' => [

                ],
                'order'      => ['title' => 'ASC'],
                'resultMode' => 'QueryBuilder'
            ])
                ->select('partial SV.{sv_id,sv_title}')
                ->indexBy('SV', 'SV.sv_id')
                ->getQuery()->getArrayResult() ?? [];

            $services = array_map(function ($item) {
                return $item['sv_title'];
            }, $services);

            $postData = [];
            if ($this->isPostRequest()) {
                $this->addSuccessMessage(
                    $this->mvcTranslate(ZF_MSG_ADD_SUCCESS)
                );

                return $this->zfRedirect()->toCurrentRoute([], ['useOldQuery' => true]);
            }

        } catch (\Throwable $e) {
            $this->saveErrorLog($e);
        }
        return new ViewModel([
            'postData'     => $postData ?? [],
            'prCates'      => $prCates ?? [],
            'services'     => $services ?? [],
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
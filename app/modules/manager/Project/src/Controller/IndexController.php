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
        try {
            $repo = $this->getEntityRepo(Project::class);
            
        } catch (\Throwable $e) {
            $this->saveErrorLog($e);
        }
        return new ViewModel([
            'postData'      => $postData ?? [],
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
}

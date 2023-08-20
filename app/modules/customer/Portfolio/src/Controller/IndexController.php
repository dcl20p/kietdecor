<?php 

declare(strict_types=1);

namespace Portfolio\Controller;
use Laminas\View\Model\ViewModel;
use Models\Entities\Project;
use Models\Entities\ProjectCate;
use Zf\Ext\Controller\ZfController;

class IndexController extends ZfController
{
    /**
     * Portfolio page
     *
     * @return ViewModel
     */
    public function indexAction(): ViewModel
    {
        $repo = $this->getEntityRepo(Project::class);
        $repoCates = $this->getEntityRepo(ProjectCate::class);
        try {
            $prcId = (int) $this->getParamsRoute('id', 0);
            $params = [];
            if (!empty($prcId)) {
                $params['prc_id'] = $prcId;
                if (empty($cates = $repoCates->find($prcId))) {
                    $this->getResponse()->setStatusCode(404);
                    return new ViewModel();
                }
            }
            $projects = $repo->fetchOpts([
                'params' => array_replace($params, [
                    'status' => 1
                ]),
                'resultMode' => 'QueryBuilder'
            ])->getQuery()->getArrayResult();

        } catch (\Throwable $e) {
            $this->saveErrorLog($e);
        }
        return new ViewModel([
            'projects'         => $projects ?? [],
            'prcId'           => $prcId ?? 0,
            'activeItemId'    => 'portfolio',
            'projectCates'    => $projectCates = $repoCates->getDataFromCache([
                'params' => [
                    'only_parent' => true
                ]
            ], false),
            'metaTitle'       => isset($projectCates[$prcId])
                                    ? $projectCates[$prcId]['prc_meta_title'] : '',
            'metaKeywords'    => isset($projectCates[$prcId])
                                    ? $projectCates[$prcId]['prc_meta_keyword'] : '',
            'metaDescription' => isset($projectCates[$prcId])
                                    ? $projectCates[$prcId]['prc_meta_desc'] : '',
        ]);
    }

    /**
     * Detail portfolio page
     *
     * @return ViewModel
     */
    public function detailAction(): ViewModel
    {
        try {
        } catch (\Throwable $e) {
            $this->saveErrorLog($e);
        }
        return new ViewModel([
            'activeItemId' => 'portfolio'
        ]);
    }
}
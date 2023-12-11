<?php 

declare(strict_types=1);

namespace Portfolio\Controller;
use Laminas\Http\PhpEnvironment\Response;
use Laminas\View\Model\JsonModel;
use Laminas\View\Model\ViewModel;
use Models\Entities\Project;
use Models\Entities\ProjectCate;
use Zf\Ext\Controller\ZfController;
use Laminas\View\Renderer\PhpRenderer;

class IndexController extends ZfController
{
    const LIMIT = 30;

    private $phpRenderer;

    public function __construct(PhpRenderer $phpRenderer)
    {
        $this->phpRenderer = $phpRenderer;
    }

    public function indexAction() 
    {
        $this->getResponse()->setStatusCode(404);
        return new ViewModel();
    }

    /**
     * Portfolio page
     *
     * @return ViewModel|Response
     */
    public function listAction(): ViewModel|Response
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
            $projects = $repo->getDataFromCacheByCate([
                'params' => array_replace($params, [
                    'status' => 1,
                    'limit'  => self::LIMIT,
                    'offset' => 0
                ])
            ]);

        } catch (\Throwable $e) {
            $this->saveErrorLog($e);
        }
        return new ViewModel([
            'projects'     => $projects ?? [],
            'prcId'        => $prcId ?? 0,
            'limit'        => self::LIMIT,
            'page'         => 1,
            'activeItemId' => 'portfolio',
            'routeName'    => $this->getCurrentRouteName(),
            'projectCates' => $projectCates = $repoCates->getDataFromCache([
                'params' => ['only_parent' => true]
            ], true),
            'metaTitle' => isset($projectCates[$prcId])
                ? $projectCates[$prcId]['prc_meta_title'] 
                : $this->mvcTranslate('Portfolio'),
            'metaKeywords' => isset($projectCates[$prcId])
                ? $projectCates[$prcId]['prc_meta_keyword'] 
                : 'portfolio',
            'metaDescription' => isset($projectCates[$prcId])
                ? $projectCates[$prcId]['prc_meta_desc'] 
                : 'Tất cả portfolio',
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

    /**
     * Load more portfolio
     *
     * @return JsonModel
     */
    public function loadMoreAction(): JsonModel
    {
        try {
            $repo  = $this->getEntityRepo(Project::class);
            $prcId = (int) $this->getParamsRoute('id', 0);
            $limit = (int) $this->getParamsQuery('limit', self::LIMIT);
            $page  = (int) $this->getParamsQuery('page', 1);
            $offset = ($page - 1) * $limit;

            $params = [];
            if ($prcId > 0) {
                $params['prc_id'] = $prcId;
            }

            $projects = $repo->getDataFromCacheByCate([
                'params' => array_replace($params, [
                    'status' => 1,
                    'limit'  => $limit,
                    'offset' => $offset
                ])
            ]);

            $html = $this->phpRenderer->render(
                (new ViewModel([
                    'projects' => $projects,
                    'limit'    => $limit,
                    'page'     => $page,
                ]))
                ->setTemplate('partial/item-portfolio.phtml')
                ->setTerminal(true)
            );

            return new JsonModel([
                'success' => true,
                'msg'     => '',
                'html'    => $html,
                'total'   => count($projects)
            ]);
            
        } catch (\Throwable $e) {
            $this->saveErrorLog($e);
        }

        return new JsonModel([
            'success' => false,
            'msg'     => $this->mvcTranslate(ZF_MSG_WENT_WRONG),
            'html'    => ''
        ]);
    }
}
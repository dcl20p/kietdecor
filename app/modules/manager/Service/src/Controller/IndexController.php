<?php

declare(strict_types=1);

namespace Service\Controller;

use Laminas\Http\PhpEnvironment\Response;
use Models\Entities\Service;
use \Zf\Ext\Controller\ZfController;
use Laminas\View\Model\ViewModel;

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
            $repo = $this->getEntityRepo(Service::class);

            $params = [
                'params' => [],
                'resultMode' => 'Query'
            ];

            $limit = $this->getParamsQuery('limit', 30);
            $page = $this->getParamsQuery('page', 1);

            $paginator = $this->getZfPaginator(
                $this->getEntityRepo(Service::class)
                    ->fetchOpts($params),
                $limit,
                $page
            );
            
        } catch (\Throwable $e) {
            $this->saveErrorLog($e);
        }

        return new ViewModel([
            'paginator' => $paginator ?? null,
            'routeName' => $this->getCurrentRouteName(),
            'pageTitle' => $this->mvcTranslate('Danh sách dịch vụ'),
            'activeItemId'  => 'service'
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
            'title'       => '',
            'description' => '',
            'status'      => 'off',
            'is_use'      => 'off'
        ]);

        $params['title'] = trim(mb_substr($params['title'], 0, 100));
        $params['description'] = trim(mb_substr($params['title'], 0, 2048));
        $params['status'] = isset($params['status']) && $params['status'] == 'on' ? 1 : 0;
        $params['is_use'] = isset($params['is_use']) && $params['is_use'] == 'on' ? 1 : 0;

        if ($params['title'] == '' || $params['description'] == '') {
            $this->addErrorMessage(
                $this->mvcTranslate(ZF_MSG_REQUIRE_DATA)
            );
            return false;
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
            $repo = $this->getEntityRepo(Service::class);
            if ($this->isPostRequest()) {
                if ($dataPost = $this->validData($this->getParamsPost())) {
                    $params = [];
                    foreach ($dataPost as $key => $item) {
                        $params["sv_{$key}"] = $item;
                    }

                    $repo->insertData(array_replace($params, [
                        'sv_code' => $this->getZfHelper()->getRandomCode([
                                        'id' => time(), 'maxLen' => 19
                                    ]),
                        'sv_create_by'    => $this->getAuthen()->adm_id,
                        'sv_created_time' => time(),
                    ]));

                    $this->addSuccessMessage(
                        $this->mvcTranslate(ZF_MSG_ADD_SUCCESS)
                    );

                    return $this->zfRedirect()->toCurrentRoute([], ['useOldQuery' => true]);
                }
            }
        } catch (\Throwable $e) {
            $this->saveErrorLog($e);
            $this->addErrorMessage(
                $this->mvcTranslate(ZF_MSG_WENT_WRONG)
            );
        }
        return new ViewModel([
            'pageTitle'     => $this->mvcTranslate('Thêm dịch vụ'),
            'routeName'     => $this->getCurrentRouteName(),
            'activeItemId'  => 'service'
        ]);
    }

    /**
     * Edit action
     *
     * @return ViewModel|Response
     */
    public function editAction(): ViewModel|Response
    {
        try {
            $repo = $this->getEntityRepo(Service::class);
            $id = $this->getParamsRoute('id', null);
            if (empty($id)
                || empty($entity = $repo->find($id))
            ) {
                $this->getResponse()->setStatusCode(404);
                return new ViewModel([]);
            }

            if ($this->isPostRequest()) {
                if ($dataPost = $this->validData($this->getParamsPost())) {
                    $params = [];
                    foreach ($dataPost as $key => $item) {
                        $params["sv_{$key}"] = $item;
                    }

                    $repo->updateData($entity, array_replace($params, [
                        'sv_edit_by'    => $this->getAuthen()->adm_id,
                        'sv_update_time' => time(),
                    ]));

                    $this->addSuccessMessage(
                        $this->mvcTranslate(ZF_MSG_UPDATE_SUCCESS)
                    );

                    return $this->zfRedirect()->toCurrentRoute([], ['useOldQuery' => true]);
                } else $postData = $this->getParamsPost();
            } else {
                $postData = [
                    'title'         => $entity->sv_title,
                    'description'   => $entity->sv_description,
                    'icon'          => $entity->sv_icon,
                    'status'        => $entity->sv_status,
                    'is_use'        => $entity->sv_is_use,
                ];
            }
        } catch (\Throwable $e) {
            $this->saveErrorLog($e);
        }

        return (new ViewModel([
            'postData'      => $postData ?? [],
            'pageTitle'     => $this->mvcTranslate('Sửa dịch vụ'),
            'routeName'     => $this->getCurrentRouteName(),
            'activeItemId'  => 'service'
        ]))->setTemplate('service/index/add.phtml');
    }

    /**
     * Delete action
     *
     * @return Response
     */
    public function deleteAction(): Response
    {
        $this->addSuccessMessage('Xoá thành công');
        return $this->zfRedirect()->toCurrentRoute(
            [], ['useOldQuery' => true]
        );
    }
}

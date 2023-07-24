<?php
namespace Manager\Controller;

use Laminas\Http\PhpEnvironment\Response;
use Laminas\View\Model\JsonModel;
use Laminas\View\Model\ViewModel;
use Models\Entities\LogError;
use Zf\Ext\Controller\ZfController;

class LogErrorController extends ZfController
{
    /**
     * List error
     *
     * @return ViewModel
     */
    public function indexAction(): ViewModel
    {
        try {
            $params = [
                'params' => array_intersect_key(
                    $this->getParamsQuery(),
                    [
                        'status' => '',
                        'uri' => '',
                        'user_id' => null,
                        'method' => '',
                        'code' => '',
                        'keyword' => ''
                    ]
                ),
                'resultMode' => 'Query'
            ];
            
            $limit = $this->getParamsQuery('limit', 30);
            $page = $this->getParamsQuery('page', 1);

            return new ViewModel([
                'paginator' => $this->getZfPaginator(
                    $this->getEntityRepo(LogError::class)
                        ->fetchOpts($params),
                    $limit,
                    $page
                ),
                'searchVals' => $params['params'],
                'routeName' => $this->getCurrentRouteName(),
                'pageTitle' => $this->mvcTranslate('Danh sách lỗi hệ thống')
            ]);
        } catch (\Throwable $e) {
            $this->saveErrorLog($e);
            $this->addErrorMessage(
                $this->mvcTranslate(ZF_MSG_WENT_WRONG)
            );
            return new ViewModel([
                'paginator' => null,
                'searchVals' => [],
                'routeName' => $this->getCurrentRouteName(),
                'pageTitle' => $this->mvcTranslate('Danh sách lỗi hệ thống')
            ]);
        }
    }

    /**
     * Delete log error
     *
     * @return Response
     */
    public function deleteAction(): Response
    {
        $routeName = $this->getCurrentRouteName();
        try {
            if ($this->isPostRequest()) {
                $ids = $this->getParamsPost('id', []);
                $repo = $this->getEntityRepo(LogError::class);

                if (!$ids
                    || empty($entities = $repo->findBy(['error_id' => $ids]))
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

    /**
     * change status
     *
     * @return JsonModel
     */
    public function changeStatusAction(): JsonModel
    {
        $id = (int) $this->getParamsRoute('id', 0);
        $status = (int) $this->getParamsPayload('status', null);

        if (!$this->isValidCsrfToken(
            null, LogError::LOG_ERROR_FOLDER_TOKEN
        )) {
            return $this->returnJsonModel(
                false, 'went_wrong', LogError::LOG_ERROR_FOLDER_TOKEN
            );
        }

        if (!in_array($status, [0, 1]) 
            || $id < 1
            || empty($entity = $this->getEntityRepo(LogError::class)->find($id))
        ) {
            return $this->returnJsonModel(
                false, 'update', LogError::LOG_ERROR_FOLDER_TOKEN
            );
        }

        try {
            $entity->error_status = $status;
            $this->flushTransaction($entity);

            $this->addSuccessMessage(
                $this->mvcTranslate(ZF_MSG_UPDATE_SUCCESS)
            );

            return $this->returnJsonModel(
                true, 'update', LogError::LOG_ERROR_FOLDER_TOKEN
            );
        } catch (\Throwable $e) {
            $this->saveErrorLog($e);
            return $this->returnJsonModel(
                false, 'update', LogError::LOG_ERROR_FOLDER_TOKEN
            );
        }
        return $this->returnJsonModel(
            false, 'update', LogError::LOG_ERROR_FOLDER_TOKEN
        );
    }
}
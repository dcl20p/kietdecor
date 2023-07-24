<?php
namespace Manager\Controller;

use Laminas\Http\PhpEnvironment\Response;
use Laminas\View\Model\ViewModel;
use Models\Entities\LogEmailError;
use Zf\Ext\Controller\ZfController;

class LogErrorEmailController extends ZfController
{
    /**
     * List error email
     *
     * @return ViewModel
     */
    public function indexAction(): ViewModel
    {
        try {
            $params = [
                'params' => array_intersect_key(
                    $this->getParamsQuery(), [
                        'keyword' => ''
                    ]
                ),
                'resultMode' => 'Query'
            ];

            $limit = $this->getParamsQuery('limit', 30);
            $page = $this->getParamsQuery('page', 1);
            return new ViewModel([
                'paginator' => $this->getZfPaginator(
                    $this->getEntityRepo(LogEmailError::class)
                        ->fetchOpts($params),
                    $limit,
                    $page
                ),
                'searchVals' => $params['params'],
                'routeName' => $this->getCurrentRouteName(),
                'pageTitle' => $this->mvcTranslate('Danh sách lỗi email')
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
                'pageTitle' => $this->mvcTranslate('Danh sách lỗi email')
            ]);
        }
    }

    /**
     * Delete log error email
     *
     * @return Response
     */
    public function deleteAction(): Response
    {
        $routeName = $this->getCurrentRouteName();
        try {
            if ($this->isPostRequest()) {
                $ids = $this->getParamsPost('id', []);
                $repo = $this->getEntityRepo(LogEmailError::class);

                if (!$ids
                    || empty($entities = $repo->findBy(['log_error_id' => $ids]))
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
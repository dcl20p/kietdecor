<?php
namespace Manager\Controller;

use Laminas\View\Model\JsonModel;
use Laminas\View\Model\ViewModel;
use Laminas\View\Renderer\PhpRenderer;
use Manager\Service\AdminManager;
use Models\Entities\AccessLog;
use Models\Entities\Admin;
use Models\Entities\Session;
use Zf\Ext\Controller\ZfController;
use Laminas\Http\PhpEnvironment\Response;

class AdminController extends ZfController
{
    use \ImageTraits\Controller\UploadImages;

    /**
     * User manager
     *
     * @var AdminManager
     */
    private AdminManager $userManager;

    /**
     * Constructor
     *
     * @param AdminManager $userManager
     */
    public function __construct($userManager)
    {
        $this->userManager = $userManager;
    }
    /**
     * My profile
     *
     * @return ViewModel
     */
    public function profileAction(): ViewModel
    {
        try {
            $id = $this->getAuthen()->adm_id ?? null;
            if (empty($id)) {
                return $this->redirectToRoute('login');
            }
            $user = $this->getEntityRepo(Admin::class)->findOneBy([
                'adm_id' => $id
            ]);

            $repoSession = $this->getEntityRepo(Session::class);

            // Get current session
            $currentSession = $repoSession->fetchOpts([
                'params' => [
                    'id' => $ssId = (session_id() ?? $user->adm_ssid),
                    'area_type' => Session::AREA_MANAGER,
                ],
                'resultMode' => 'getQuery',
            ])
            ->indexBy('SS', 'SS.ss_id')
            ->getQuery()->getArrayResult();
            
            $sessions = $repoSession->fetchOpts([
                'params' => [
                    'user_id' => $id,
                    'area_type' => Session::AREA_MANAGER,
                    'not_include_id' => $ssId,
                    'limit_offset' => [
                        'limit' => Session::LIMIT_LOAD_MORE,
                        'offset' => 0,
                    ]
                ],
                'resultMode' => 'getQuery',
                'order' => [
                    'is_login' => 'DESC'
                ]
            ])
            ->indexBy('SS', 'SS.ss_id')
            ->getQuery()->getArrayResult();

            // Add current session to the beginning of the list
            $sessions = array_replace($currentSession, $sessions);

            return new ViewModel([
                'user'            => $user ?? null,
                'sessions'        => $sessions ?? null,
                'routeName'       => $this->getCurrentRouteName(),
                'pageTitle'       => $this->mvcTranslate('Tài khoản của tôi'),
                'showBtnLoadmore' => count($sessions) >= Session::LIMIT_LOAD_MORE
            ]);
        } catch (\Throwable $e) {
            $this->saveErrorLog($e);
            $this->addErrorMessage(
                $this->mvcTranslate(ZF_MSG_WENT_WRONG)
            );
            return new ViewModel([
                'user'           => null,
                'sessions'       => null,
                'routeName'      => $this->getCurrentRouteName(),
                'pageTitle'      => $this->mvcTranslate('Tài khoản của tôi'),
            ]);
        }
    }

    /**
     * Upload avatar into server
     *
     * @return JsonModel
     */
    public function uploadAvatarAction(): JsonModel
    {
        try {
            if ($this->isPostRequest()) {
                if (!$this->isValidCsrfToken(null, Admin::PROFILE_FOLDER_TOKEN)) {
                    return $this->returnJsonModel(
                        false, 
                        'went_wrong',
                        Admin::PROFILE_FOLDER_TOKEN
                    );
                }
                $file = $this->getParamsFiles('file', []);
                $userId = (int) $this->getParamsPost('id', 0);
                if (empty($userId) || empty($file)) {
                    return $this->returnJsonModel(
                        false, 
                        'not_empty',
                        Admin::PROFILE_FOLDER_TOKEN
                    );
                }
                $user = $this->getEntityRepo(Admin::class)->find($userId);

                if (empty($user)) {
                    return $this->returnJsonModel(
                        false, 
                        'not_exists',
                        Admin::PROFILE_FOLDER_TOKEN
                    );
                }

                if ($this->getAuthen()->adm_id != $user->adm_id) {
                    $this->blockUser($user);
                    return $this->returnJsonModel(false, 'went_wrong', Admin::PROFILE_FOLDER_TOKEN);
                }

                if (!empty($this->isValidUploadImg($file))) {
                    $upload = $this->uploadImage($file, $user->adm_code);
                    if (false !== $upload) {
                        $oldAvatar = $user->adm_avatar;
                        $user->adm_avatar = $upload['name'];

                        // Update to db & update authen
                        $this->flushTransaction($user);
                        $this->getAuthen()->adm_avatar = $upload['name'];

                        if (!empty($oldAvatar)) {
                            @unlink("{$upload['path']}/{$oldAvatar}");
                        }
                    } else {
                        return $this->returnJsonModel(
                            false, 
                            'went_wrong', 
                            Admin::PROFILE_FOLDER_TOKEN,
                            $this->mvcTranslate('Hình ảnh được chọn không thể upload, vui lòng chọn hình ảnh khác.')
                        );
                    }
                }
                
                return $this->returnJsonModel(
                    true,
                    'upload',
                    Admin::PROFILE_FOLDER_TOKEN
                );
            } else {
                return $this->returnJsonModel(
                    false, 
                    'not_allow',
                    Admin::PROFILE_FOLDER_TOKEN
                );
            }
        } catch (\Throwable $e) {
            if (!empty($upload['name'])) {
                @unlink("{$upload['path']}/{$upload['name']}");
            }
            $this->saveErrorLog($e);
            return $this->returnJsonModel(
                false, 
                'went_wrong', 
                Admin::PROFILE_FOLDER_TOKEN
            );
        }
    }

    /**
     * Upload background timeline to server
     *
     * @return JsonModel
     */
    public function uploadBgTimeLineAction(): JsonModel
    {
        try {
            if ($this->isPostRequest()) {
                if (!$this->isValidCsrfToken(null, Admin::PROFILE_FOLDER_TOKEN)) {
                    return $this->returnJsonModel(
                        false, 
                        'went_wrong',
                        Admin::PROFILE_FOLDER_TOKEN
                    );
                }
                $file = $this->getParamsFiles('file', []);
                $userId = (int) $this->getParamsPost('id', 0);
                if (empty($userId) || empty($file)) {
                    return $this->returnJsonModel(
                        false, 
                        'not_empty',
                        Admin::PROFILE_FOLDER_TOKEN
                    );
                }
                $user = $this->getEntityRepo(Admin::class)->find($userId);

                if (empty($user)) {
                    return $this->returnJsonModel(
                        false, 
                        'not_exists',
                        Admin::PROFILE_FOLDER_TOKEN
                    );
                }

                if ($this->getAuthen()->adm_id != $user->adm_id) {
                    $this->blockUser($user);
                    return $this->returnJsonModel(false, 'went_wrong', Admin::PROFILE_FOLDER_TOKEN);
                }

                if (!empty($this->isValidUploadImg($file))) {
                    $upload = $this->uploadImage($file, $user->adm_code, 1000, 300);
                    if (false !== $upload) {
                        $oldBgTimeline = $user->adm_bg_timeline;
                        $user->adm_bg_timeline = $upload['name'];

                        // Update to db & update authen
                        $this->flushTransaction($user);
                        $this->getAuthen()->adm_bg_timeline = $upload['name'];

                        if (!empty($oldBgTimeline)) {
                            @unlink("{$upload['path']}/{$oldBgTimeline}");
                        }
                    } else {
                        return $this->returnJsonModel(
                            false, 
                            'went_wrong', 
                            Admin::PROFILE_FOLDER_TOKEN,
                            $this->mvcTranslate('Hình ảnh được chọn không thể upload, vui lòng chọn hình ảnh khác.')
                        );
                    }
                }

                return $this->returnJsonModel(
                    true,
                    'upload',
                    Admin::PROFILE_FOLDER_TOKEN
                );
            } else {
                return $this->returnJsonModel(
                    false, 
                    'not_allow',
                    Admin::PROFILE_FOLDER_TOKEN
                );
            }
        } catch (\Throwable $e) {
            if (!empty($upload['name'])) {
                @unlink("{$upload['path']}/{$upload['name']}");
            }
            $this->saveErrorLog($e);
            return $this->returnJsonModel(
                false, 
                'went_wrong', 
                Admin::PROFILE_FOLDER_TOKEN
            );
        }
    }

    /**
     * Change status user
     *
     * @return JsonModel
     */
    public function changeStatusProfileAction(): JsonModel
    {
        $userId = (int) $this->getParamsPayload('id', 0);
        $status = $this->getParamsPayload('status', null);
        
        if (!$this->isValidCsrfToken(null, Admin::PROFILE_FOLDER_TOKEN)) {
            return $this->returnJsonModel(false, 'went_wrong', Admin::PROFILE_FOLDER_TOKEN);
        }
        if (!in_array($status, [0, 1])
            || $userId < 1
            || empty($user = $this->getEntityRepo(Admin::class)->find($userId))
            || $user->adm_groupcode == Admin::GROUP_SUPPORT
        ) {
            return $this->returnJsonModel(false, 'update', Admin::PROFILE_FOLDER_TOKEN);
        }

        try {
            if ($this->getAuthen()->adm_id != $user->adm_id) {
                $this->blockUser($user);
                return $this->returnJsonModel(false, 'went_wrong', Admin::PROFILE_FOLDER_TOKEN);
            }
            $user->adm_status = $status;
            $this->flushTransaction($user);
            $this->getAuthen()->adm_status = $status;

            if ($status == 0) {
                $this->deleteSession($user->adm_id);
            }
            return $this->returnJsonModel(true, 'update', Admin::PROFILE_FOLDER_TOKEN);
            
        } catch (\Throwable $e) {
            $this->saveErrorLog($e);
            return $this->returnJsonModel(false, 'update', Admin::PROFILE_FOLDER_TOKEN);
        }
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
            null, Admin::USER_FOLDER_TOKEN
        )) {
            return $this->returnJsonModel(
                false, 'went_wrong', Admin::USER_FOLDER_TOKEN
            );
        }

        if (!in_array($status, [0, 1]) 
            || $id < 1
            || empty($entity = $this->getEntityRepo(Admin::class)->find($id))
            || $entity->adm_groupcode == Admin::GROUP_SUPPORT
        ) {
            return $this->returnJsonModel(
                false, 'update', Admin::USER_FOLDER_TOKEN
            );
        }

        try {
            $entity->adm_status = $status;
            $this->flushTransaction($entity);

            if ($status == 0) {
                $this->deleteSession($entity->adm_id);
            }

            $this->addSuccessMessage(
                $this->mvcTranslate(ZF_MSG_UPDATE_SUCCESS)
            );

            return $this->returnJsonModel(
                true, 'update', Admin::USER_FOLDER_TOKEN
            );
        } catch (\Throwable $e) {
            $this->saveErrorLog($e);
            return $this->returnJsonModel(
                false, 'update', Admin::USER_FOLDER_TOKEN
            );
        }
        return $this->returnJsonModel(
            false, 'update', Admin::USER_FOLDER_TOKEN
        );
    }

    /**
     * Block user
     *
     * @param Admin $user
     * @return void
     */
    protected function blockUser(Admin $user): void
    {
        $user->adm_status = 0;
        $this->flushTransaction($user);
        if ($user->adm_ssid) {
            $this->getEntityRepo(Admin::class)->delSessionData($user->adm_ssid);
        }
    }

    /**
     * check validate data post
     *
     * @param array $params
     * @return array
     */
    protected function validDataPostUser(array $params): array
    {
        $params = array_intersect_key(
            $params, [
                'id' => 0,
                'first_name' => '',
                'last_name' => '',
                'phone' => '',
                'phone_code' => '',
                'email' => '',
                'confirm_email' => '',
                'gender' => '',
                'address' => '',
                'birthday_year' => 0,
                'birthday_month' => 0,
                'birthday_day' => 0,
            ]
        );

        $result = $this->validData($params);

        $result['adm_birthday'] = strtotime(
            "{$params['birthday_year']}-{$params['birthday_month']}-{$params['birthday_day']}"
        );

        return [
            'success' => true,
            'data' => $result,
            'msg' => '',
        ];
    }

    /**
     * Update information of user
     *
     * @return JsonModel
     */
    public function updateInfoUserAction(): JsonModel
    {
        $needRollback = false;
        try {
            $repo = $this->getEntityRepo(Admin::class);
            if ($this->isPostRequest()) {
                if ($this->isValidCsrfToken(null, Admin::PROFILE_FOLDER_TOKEN)) {
                    $data = $this->validDataPostUser($this->getParamsPayload());
                    if ($data['success'] === true && !empty($data['data'])) {
                        if (empty($user = $repo->find(
                            $this->getParamsPayload('id', null)
                        ))) {
                            return $this->returnJsonModel(
                                false, 'not_exists', Admin::PROFILE_FOLDER_TOKEN
                            );
                        }

                        $isSameUser = $this->getAuthen()->adm_id == $user->adm_id;
                        $oldGroup = $user->adm_groupcode;
                        $oldName = $user->adm_fullname;

                        $this->startTransaction();
                        $needRollback = true;
    
                        $this->getEntityRepo(Admin::class)
                            ->updateData($user, array_replace(
                                $data['data'], [
                                    'adm_update_time' => time()
                                ]
                            ));
    
                        $this->commitTransaction();
                        $needRollback = false;

                        if ($isSameUser) {
                            foreach (['adm_fullname', 'adm_email', 'adm_phone'] as $col) {
                                $this->getAuthen()->{$col} = $user->{$col};
                            }
                        } else if ($oldGroup != $user->adm_groupcode
                            || $oldName != $user->adm_fullname
                        ) {
                            $this->blockUser($user);
                            return $this->returnJsonModel(false, 'went_wrong', Admin::PROFILE_FOLDER_TOKEN);
                        }
    
                        return $this->returnJsonModel(true, 'update', Admin::PROFILE_FOLDER_TOKEN);
                    } else {
                        return $this->returnJsonModel(false, 'update', Admin::PROFILE_FOLDER_TOKEN, $data['msg']);
                    }
                } else {
                    return $this->returnJsonModel(false, 'went_wrong', Admin::PROFILE_FOLDER_TOKEN);
                }
            } else {
                return $this->returnJsonModel(false, 'not_allow', Admin::PROFILE_FOLDER_TOKEN);
            }
        } catch (\Throwable $e) {
            if ($needRollback) $this->getEntityManager()->rollback();
            $this->saveErrorLog($e);
            return $this->returnJsonModel(false, 'went_wrong', Admin::PROFILE_FOLDER_TOKEN);
        }
    }

    /**
     * valid data change password
     *
     * @param array $params
     * @return array
     */
    protected function validDataPostChangePass(array $params): array
    {
        $params = array_intersect_key(
            $params, [
                'id' => 0,
                'current_pass' => '',
                'new_pass' => '',
                'confirm_new_pass' => ''
            ]
        );
        foreach (['current_pass', 'new_pass', 'confirm_new_pass']
            as $val
        ) {
            if (strlen($params[$val]) < 6 || strlen($params[$val]) > 50) {
                return [
                    'success' => false,
                    'msg' => $this->mvcTranslate(ZF_MSG_PASSWORD_INVALID)
                ];
            }

            if (!preg_match(
                '/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[@$!%*#?&,\-_+;])[a-zA-Z\d@$!%*#?&,\-_+;]{6,50}$/',
                $params[$val], $matches
            )) {
                return [
                    'success' => false,
                    'msg' => $this->mvcTranslate(ZF_MSG_PASSWORD_INVALID)
                ];
            }
        }

        if ($params['new_pass'] !== $params['confirm_new_pass']) {
            return [
                'success' => false,
                'msg' => $this->mvcTranslate(ZF_MSG_NOT_MATCH)
            ];
        }

        return [
            'success' => true,
            'data' => $params,
            'msg' => ''
        ];
    }

    /**
     * Change password
     *
     * @return JsonModel
     */
    public function changePasswordAction(): JsonModel
    {
        try {
            $repo = $this->getEntityRepo(Admin::class);
            if ($this->isPostRequest()) {
                if ($this->isValidCsrfToken(null, Admin::PROFILE_FOLDER_TOKEN)) {
                    $data = $this->validDataPostChangePass(
                        $this->getParamsPayload()
                    );

                    if ($data['success'] === true && !empty($data['data'])) {
                        $user = $repo->find($data['data']['id']);
                        if ($this->userManager->changePassword($user, $data['data'])) {
                            return $this->returnJsonModel(true, 'update', Admin::PROFILE_FOLDER_TOKEN);
                        } else {
                            return $this->returnJsonModel(
                                false, 'update', Admin::PROFILE_FOLDER_TOKEN,
                                $this->mvcTranslate('Mật khẩu hiện tại không đúng, vui lòng thử lại')
                            );
                        }
                    } else {
                        return $this->returnJsonModel(false, 'update', Admin::PROFILE_FOLDER_TOKEN, $data['msg']);
                    }
                } else {
                    return $this->returnJsonModel(false, 'went_wrong', Admin::PROFILE_FOLDER_TOKEN);
                }
            } else {
                return $this->returnJsonModel(false, 'not_allow', Admin::PROFILE_FOLDER_TOKEN);
            }
        } catch (\Throwable $e) {
            $this->saveErrorLog($e);
            return $this->returnJsonModel(false, 'went_wrong', Admin::PROFILE_FOLDER_TOKEN);
        }
    }

    /**
     * Load more session
     *
     * @return JsonModel
     */
    public function loadMoreSessionAction(): JsonModel
    {
        try {
            $repo = $this->getEntityRepo(Session::class);
            $page = (int) $this->getParamsQuery('page', 1);

            $limit = Session::LIMIT_LOAD_MORE;
            $offset = ($page - 1) * $limit;

            $sessions = $repo->fetchOpts([
                'params' => [
                    'user_id' => $id = $this->getAuthen()->adm_id ?? null,
                    'area_type' => Session::AREA_MANAGER,
                    'not_include_id' => session_id(),
                    'limit_offset' => [
                        'limit' => Session::LIMIT_LOAD_MORE,
                        'offset' => $offset,
                    ]
                ],
                'resultMode' => 'Array',
                'order' => [
                    'is_login' => 'DESC'
                ]
            ]);
            
            $service = $this->getEvent()->getApplication()->getServiceManager();
            $phpRender = null;
            if ($service->has(PhpRenderer::class)) {
                $phpRender = $service->get(PhpRenderer::class);
            } else $phpRender = new PhpRenderer();

            $html = $phpRender->render(
                (new ViewModel([
                    'sessions' => $sessions,
                    'page' => $page,
                ]))
                ->setTemplate('partial/admin/item-session.phtml')
                ->setTerminal(true)
            );

            return new JsonModel([
                'success' => true,
                'html' => $html,
                'limit' => $limit,
                'total' => count($sessions),
            ]);

        } catch (\Throwable $e) {
            $this->saveErrorLog($e);
            return $this->returnJsonModel(false, 'went_wrong');
        }
    }

    /**
     * get data detail of session
     *
     * @return JsonModel
     */
    public function getDetailSessionAction(): JsonModel
    {
        try {
            $repo = $this->getEntityRepo(Session::class);
            $ssID = $this->getParamsQuery('ss_id', '');

            $session = $repo->find($ssID);

            if (!empty($session)) {
                $service = $this->getEvent()->getApplication()->getServiceManager();
                $phpRender = null;
                if ($service->has(PhpRenderer::class)) {
                    $phpRender = $service->get(PhpRenderer::class);
                } else $phpRender = new PhpRenderer();

                $html = $phpRender->render(
                    (new ViewModel([
                        'session' => $session,
                    ]))
                    ->setTemplate('partial/admin/item-detail-session.phtml')
                    ->setTerminal(true)
                );

                return new JsonModel([
                    'success' => true,
                    'html' => $html,
                ]);
            }
        } catch (\Throwable $e) {
            $this->saveErrorLog($e);
        }
        return $this->returnJsonModel(false, 'went_wrong');
    }

    /**
     * Log out session of user
     *
     * @return JsonModel
     */
    public function logOutSessionAction(): JsonModel
    {
        try {
            if ($this->isPostRequest()) {
                if ($this->isValidCsrfToken(null, Admin::PROFILE_FOLDER_TOKEN)) {
                    $ssId = $this->getParamsPayload('ss_id', '');

                    if (empty($ssId)) return $this->returnJsonModel(false, 'not_exists', Admin::PROFILE_FOLDER_TOKEN);
                    
                    if ($ssId !== session_id()) {
                        $this->getEntityRepo(Admin::class)->delSessionData($ssId);
                        return $this->returnJsonModel(true, 'update', Admin::PROFILE_FOLDER_TOKEN);
                    }
                }
            }

        } catch (\Throwable $e) {
            $this->saveErrorLog($e);
        }

        return $this->returnJsonModel(false, 'went_wrong', Admin::PROFILE_FOLDER_TOKEN);
    }

    /**
     * Delete user
     *
     * @return JsonModel
     */
    public function deleteProfileAction(): JsonModel
    {
        $needRollback = false;
        try {
            if ($this->isPostRequest()) {
                if ($this->isValidCsrfToken(null, Admin::PROFILE_FOLDER_TOKEN)) {
                    $admId = (int) $this->getParamsPayload('adm_id', '');
                    
                    $repo = $this->getEntityRepo(Admin::class);
                    
                    if ($admId !== $this->getAuthen()->adm_id
                        || $admId < 1
                        || empty($user = $repo->find($admId))
                        || $user->adm_groupcode == Admin::GROUP_SUPPORT
                    ) {
                        return $this->returnJsonModel(false, 'not_exists', Admin::PROFILE_FOLDER_TOKEN);
                    }
                    $this->startTransaction();
                    $needRollback = true;
                    $this->deleteSession($user->adm_id);
                    $repo->delData($user);
                    $this->commitTransaction();
                    $needRollback = false;

                    return $this->returnJsonModel(true, 'update', Admin::PROFILE_FOLDER_TOKEN);
                }
            } else {
                return $this->returnJsonModel(false, 'not-allow', Admin::PROFILE_FOLDER_TOKEN);
            }

        } catch (\Throwable $e) {
            if ($needRollback) $this->rollbackTransaction();
            $this->saveErrorLog($e);
        }

        return $this->returnJsonModel(false, 'went_wrong', Admin::PROFILE_FOLDER_TOKEN);
    }

    /**
     * Delete session
     *
     * @param integer $userId
     * @return boolean
     */
    private function deleteSession(int $userId): bool
    {
        if (empty($userId)) return false;
        
        $repoAdmin = $this->getEntityRepo(Admin::class);
        $repoSession = $this->getEntityRepo(Session::class);
        $sessions = $repoSession->findBy([
            'ss_user_id' => $userId,
            'ss_area_type' => Session::AREA_MANAGER
        ]);
        
        $ssIds = [];
        foreach ($sessions ?? [] as $session) {
            $ssIds[] = $session->ss_id;
        }

        if (!empty($ssIds))
            $repoAdmin->delMultiSessionData($ssIds);

        return true;
    }

    /**
     * Delete log error
     *
     * @return Response
     */
    public function deleteAction(): Response
    {
        $needRollback = false;
        $routeName = $this->getCurrentRouteName();
        try {
            if ($this->isPostRequest()) {
                $ids = $this->getParamsPost('id', []);
                $repo = $this->getEntityRepo(Admin::class);

                if (!$ids
                    || empty($entities = $repo->findBy(['adm_id' => $ids]))
                ) {
                    $this->addErrorMessage(
                        $this->mvcTranslate(ZF_MSG_DATA_NOT_EXISTS)
                    );
                    return $this->redirectToRoute($routeName, [], ['useOldQuery' => true]);
                }

                $this->startTransaction();
                $needRollback = true;
                foreach ($entities as $entity) {
                    if ($entity->adm_groupcode != Admin::GROUP_SUPPORT) {
                        $this->deleteSession($entity->adm_id);
                        $repo->delData($entity);
                    }
                }
                $this->commitTransaction();
                $needRollback = false;
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
            if ($needRollback) $this->rollbackTransaction();
            $this->saveErrorLog($e);
            $this->addSuccessMessage(
                $this->mvcTranslate(ZF_MSG_DEL_FAIL)
            );
        }
        return $this->redirectToRoute($routeName, [], ['useOldQuery' => true]);
    }

    /**
     * Listing
     *
     * @return ViewModel
     */
    public function indexAction(): ViewModel
    {
        try {
            $params = [
                'params' => array_replace(
                    array_intersect_key($this->getParamsQuery(), [
                        'keyword' => '',
                        'status' => '',
                    ]),
                    [
                        'groupcode' => array_values(Admin::returnSearchGroupCodesByCode(
                            $this->getAuthen()->adm_groupcode
                        ))
                    ]
                ),
                'resultMode' => 'Query'
            ];
            $paginator = $this->getZfPaginator(
                $this->getEntityRepo(Admin::class)
                    ->fetchOpts($params),
                $this->getParamsQuery('limit', 30),
                $this->getParamsQuery('page', 1)
            );
        } catch (\Throwable $e) {
            dd($e->getMessage(), $e->getTraceAsString());
            $this->saveErrorLog($e);
            $this->addErrorMessage(
                $this->mvcTranslate(ZF_MSG_WENT_WRONG)
            );
        }

        return new ViewModel([
            'paginator'    => $paginator ?? null,
            'pageTitle'    => $this->mvcTranslate('Danh sách người dùng'),
            'routeName'    => $this->getCurrentRouteName(),
            'activeItemId' => 'user_list',
            'repoAccLog'   => $this->getEntityRepo(AccessLog::class)
        ]);
    }

    /**
     * General function for valid data user
     *
     * @param array $params
     * @return array
     */
    protected function validData(array $params): array
    {
        foreach ([
            'first_name' => 100,
            'last_name' => 100,
            'phone' => 14,
            'email' => 100,
            'confirm_email' => 100,
            'address' => 100,
        ] as $key => $val) {
            $params[$key] = htmlspecialchars(
                trim(mb_substr($params[$key], 0, $val))
            );
        }

        if ('' === $params['first_name']) {
            return [
                'success' => false,
                'msg' => $this->mvcTranslate('Vui lòng nhập tên'),
            ];
        }
        if ('' === $params['last_name']) {
            return [
                'success' => false,
                'msg' => $this->mvcTranslate('Vui lòng nhập họ'),
            ];
        }
        if ('' === $params['email'] || empty(filter_var($params['email'], FILTER_VALIDATE_EMAIL))) {
            return [
                'success' => false,
                'msg' => $this->mvcTranslate('Vui lòng nhập email'),
            ];
        }
        if ('' === $params['confirm_email'] || empty(filter_var($params['confirm_email'], FILTER_VALIDATE_EMAIL))) {
            return [
                'success' => false,
                'msg' => $this->mvcTranslate('Vui lòng nhập email xác nhận'),
            ];
        }
        if ($params['email'] !== $params['confirm_email']) {
            return [
                'success' => false,
                'msg' => $this->mvcTranslate(ZF_MSG_EMAIL_NOT_MATCH),
            ];
        }
        if ('' === ($params['phone'] = preg_replace('/[^0-9]/m', '', $params['phone']))) {
            return [
                'success' => false,
                'msg' => $this->mvcTranslate('Vui lòng nhập số điện thoại'),
            ];
        }

        if (!in_array($params['gender'], [
            Admin::GENDER_FEMALE,
            Admin::GENDER_MALE,
            Admin::GENDER_OTHER
        ])) {
            return [
                'success' => false,
                'msg' => $this->mvcTranslate('Giới tính không đúng'),
            ];
        }

        if (!in_array($params['group_code'], [
            Admin::GROUP_STAFF,
            Admin::GROUP_MANAGER,
            Admin::GROUP_SUPPER_ADMIN,
            Admin::GROUP_SUPPORT,
            Admin::GROUP_TESTER
        ])) {
            return [
                'success' => false,
                'msg' => $this->mvcTranslate('Nhóm tài khoản không đúng'),
            ];
        }

        $result = [];

        foreach ($params as $key => $value) {
            if (in_array($key, [
                'gender',
                'email',
                'address',
                'phone',
                'phone_code',
            ])) {
                $result["adm_{$key}"] = $value;
            }
        }
        $result['adm_json_name'] = [
            'first' => $params['first_name'],
            'last' => $params['last_name'],
        ];
        $result['adm_fullname'] = "{$params['last_name']} {$params['first_name']}";

        return $result;
    }

    /**
     * Check valid data post add user
     *
     * @param array $params
     * @return array
     */
    protected function validDataPostAddUser(array $params): array
    {
        $params = array_intersect_key($params, [
            "first_name"           => "",
            "last_name"            => "",
            "gender"               => "",
            "birthday"             => [],
            "email"                => "",
            "confirm_email"        => "",
            "address"              => "",
            "phone_code"           => "",
            "phone"                => "",
            "group_code"           => "",
            "new_password"         => "",
            "confirm_new_password" => ""
        ]);

        $result = $this->validData($params);

        $result['adm_birthday'] = strtotime(
            "{$params['birthday']['year']}-{$params['birthday']['month']}-{$params['birthday']['day']}"
        );

        $result['adm_groupcode'] = $params['group_code'];
        $result['adm_password'] = $params['new_password'];

        return [
            'success' => true,
            'data' => $result,
            'msg' => '',
        ];
    }

    /**
     * Add new user admin
     *
     * @return ViewModel
     */
    
     public function addAction()
    {
        $needRollback = true;
        try {
            if (!in_array(
                $this->getAuthen()->adm_groupcode, 
                Admin::returnGroupCodesForManager()
            )) {
                $this->getResponse()->setStatusCode(404);
            }
            if ($this->isPostRequest()) {
                $data = $this->validDataPostAddUser(
                    $this->getParamsPost()
                );

                if ($data['success'] === true && !empty($data['data'])) {
                    if (!$this->userManager->checkUserExists($data['data']['adm_email'])) {
                        $needRollback = true;
                        $this->startTransaction();
                        $this->handleUpsertUser($data);
                        $this->flushTransaction();
                        $this->commitTransaction();
                        $needRollback = false;

                        $this->addSuccessMessage($this->mvcTranslate(ZF_MSG_ADD_SUCCESS));
                        return $this->redirectToRoute(
                            [], ['useOldQuery' => true]
                        );
                    } else {
                        $this->addErrorMessage(
                            $this->mvcTranslate('Email đã tồn tại trên hệ thống')
                        );
                    }
                } else {
                    $this->addErrorMessage($this->mvcTranslate(ZF_MSG_WENT_WRONG));
                }
            }
        } catch (\Throwable $e) {
            if ($needRollback) $this->getEntityManager()->rollback();
            $this->saveErrorLog($e);
        }

        return new ViewModel([
            'user'         => null,
            'pageTitle'    => $this->mvcTranslate('Thêm người dùng'),
            'routeName'    => $this->getCurrentRouteName(),
            'activeItemId' => 'user_add',
            'isEdit'       => false
        ]);
    }

    /**
     * Handle common for insert or update user admin
     *
     * @param array $data
     * @param boolean $isEdit
     * @param Admin|null $user
     * @return bool
     */
    protected function handleUpsertUser(array $data, bool $isEdit = false, ?Admin $user = null): bool
    {
        if (!empty($data)) {
            $avatar = $this->getParamsFiles('avatar', []);
            $bgTimeline = $this->getParamsFiles('bg-timeline', []);
    
            $repo = $this->getEntityRepo(Admin::class);
            if (!$isEdit) {
                $insertData = array_replace($data['data'], [
                    'adm_code' => $this->getZfHelper()->getRandomCode([
                        'id' => time(), 'maxLen' => 19
                    ]),
                    'adm_status' => 1,
                    'adm_create_time' => time(),
                ]);
    
                // Insert basic info
                $response = $repo->insertData($insertData);
            } else {
                $updateData = array_replace($data['data'], [
                    'adm_update_time' => time()
                ]);
    
                // Update basic info
                $response = $repo->updateData($user, $updateData);
            }
    
            // Insert avatar
            if (!empty($this->isValidUploadImg($avatar))) {
                $upload = $this->uploadImage($avatar, $response->adm_code);
                if (false !== $upload) {
                    $response->adm_avatar = $upload['name'];
                }
            }
    
            // Insert background time line
            if (!empty($this->isValidUploadImg($bgTimeline))) {
                $uploadBg = $this->uploadImage($bgTimeline, $response->adm_code, 1000, 300);
                if (false !== $uploadBg) {
                    $response->adm_bg_timeline = $uploadBg['name'];
                }
            }

            return true;
        }
        return false;   
    }

    /**
     * edit user admin
     *
     * @return ViewModel
     */
    public function editAction(): ViewModel
    {
        try {
            $repo = $this->getEntityRepo(Admin::class);
            $id = $this->getParamsRoute('id', 0);
            if (empty($id)
                || empty($user = $repo->findOneBy(['adm_id' => $id]))
                || !in_array(
                    $this->getAuthen()->adm_groupcode, 
                    Admin::returnGroupCodesForManager()
                )
            ) {
                $this->getResponse()->setStatusCode(404);
            }
            if ($this->isPostRequest()) {
                $data = $this->validDataPostAddUser(
                    $this->getParamsPost()
                );

                if ($data['success'] === true && !empty($data['data'])) {
                    if (empty($data['data']['adm_password'])) {
                        unset($data['data']['adm_password']);
                    }
                    $needRollback = true;
                    $this->startTransaction();
                    $this->handleUpsertUser($data, true, $user);
                    $this->flushTransaction();
                    $this->commitTransaction();
                    $needRollback = false;

                    $this->addSuccessMessage($this->mvcTranslate(ZF_MSG_UPDATE_SUCCESS));
                    return $this->redirectToRoute(
                        [], ['useOldQuery' => true]
                    );
                } else {
                    $this->addErrorMessage($this->mvcTranslate(ZF_MSG_WENT_WRONG));
                }
            }
        } catch (\Throwable $e) {
            if ($needRollback) $this->getEntityManager()->rollback();
            $this->saveErrorLog($e);
        }

        return (new ViewModel([
            'user'          => $user ?? null,
            'pageTitle'     => $this->mvcTranslate('Thêm người dùng'),
            'routeName'     => $this->getCurrentRouteName(),
            'activeItemId'  => 'user_add',
            'isEdit'        => true
        ]))->setTemplate('/manager/admin/add.phtml');
    }
}
<?php
namespace Manager\Controller;

use Laminas\Config\Writer\PhpArray;
use Laminas\Http\PhpEnvironment\Response;
use Laminas\View\Model\ViewModel;
use Models\Entities\Admin;
use Models\Utilities\AppUtilities;
use Models\Utilities\User;
use Zf\Ext\Controller\ZfController;

class PermissionController extends ZfController
{
    public function __construct(
        private array $configs, 
        private array $routerTypes
    ) {}
    /**
     * Listing of permission
     *
     * @return ViewModel
     */
    public function indexAction(): ViewModel
    {
        try {
            if ($this->isPostRequest()) {
                return $this->savePermission(
                    $this->getParamsPost()
                );
            }
        } catch (\Throwable $e) {
            $this->saveErrorLog($e);
        }

        return new ViewModel([
            'checkVal'  => $this->getParamsQuery('gcode', ''),
            'oldData'   => $this->getPostFromCache(),
            'items'     => AppUtilities::extractAllActionOfCtr(
                $this->configs, $this->routerTypes
            ),
            'pageTitle' => $this->mvcTranslate('Phân quyền'),
            'routeName' => $this->getCurrentRouteName(),
            'activeItemId' => 'system_permission'
        ]);
    }

    /**
     * Refresh cache
     *
     * @return Response
     */
    public function refreshCacheAction(): Response
    {
        AppUtilities::extractAllActionOfCtr(
            $this->configs, $this->routerTypes, false
        );
        return ($this->redirectToRoute(
            $this->getCurrentRouteName()
        ));
    }

    /**
     * Clear old permission
     *
     * @param string $groupName
     * @param string $pathFolder
     * @param array $pld
     * @param array $new
     * @return integer
     */
    protected function clearOldData(string $groupName, string $pathFolder, array $pld = [], array $new = []): int
    {
        if (empty($old)) return 0;

        $rmItems = array_diff($old, $new);
        $total = count($rmItems);

        if ($total) {
            $writer = new PhpArray();
            foreach ($rmItems as $rmItem) {
                $total++;
                $path = implode(DIRECTORY_SEPARATOR, [
                    $pathFolder, crc32($rmItem) . '.php'
                ]);

                $data = (@include $path) ?? [];

                if (empty($data)) @unlink($path);
                else {
                    unset($data[crc32($groupName)]);
                    $writer->toFile($path, $data);
                }
            }
        }
        return $total;
    }

    /**
     * Save post data to cache
     *
     * @param string $groupName
     * @param array $postData
     * @return mixed
     */
    protected function savePostToCache(string $groupName, array $postData = []): mixed
    {
        return @file_put_contents(implode(DIRECTORY_SEPARATOR, [
            DATA_PATH, 'zf_acl', APPLICATION_SITE, 'logs', crc32($groupName) . '_log.dat'
        ]), User::encodeStr(serialize($postData)));
    }

    /**
     * Save permission
     *
     * @param array $postData
     * @return void
     */
    protected function savePermission(array $postData = [])
    {
        $modules = $postData['actions'] ?? [];
        $groupName = $postData['group'] ?? '';

        if (!empty($groupName)) {
            $basePath = implode(DIRECTORY_SEPARATOR, [
                DATA_PATH, 'zf_acl', APPLICATION_SITE
            ]);

            $oldData = $this->getPostFromCache([$groupName])[$groupName] ?? [];

            $writer = new PhpArray();
            $writer->setUseBracketArraySyntax((true));
            foreach ($modules as $module =>$acts) {
                $path = implode(DIRECTORY_SEPARATOR, [
                    $basePath, crc32($module)  
                ]);

                if (!realpath(($path))) {
                    @mkdir($path, 0755);
                }

                // Clear old action
                $this->clearOldData(
                    $groupName, $path, $oldData[$module] ?? [], $acts
                );

                // Add new action
                foreach ($acts as $act) {
                    $filePath = implode(DIRECTORY_SEPARATOR, [
                        $path, crc32($act) . '.php'
                    ]);

                    $data = (@include $filePath);

                    if (empty($data)) $data = [];
                    $data[crc32($groupName)] = rand(1, 1000);
                    $writer->toFile($filePath, $data);
                }

                // Save to cache
                $this->savePostToCache($groupName, [
                    $groupName => $modules
                ]);
            }

            return $this->redirectToRoute(
                $this->getCurrentRouteName()
            );
        }
    }

    /**
     * Get post from cache
     *
     * @param array $groups
     * @return array
     */
    protected function getPostFromCache(array $groups = []): array
    {
        if (empty($groups)) {
            $groups = Admin::returnGroupCodes();
        }

        $items = [];
        $basePath = implode(DIRECTORY_SEPARATOR, [
            DATA_PATH, 'zf_acl', APPLICATION_SITE, 'logs'
        ]);

        foreach ($groups as $group) {
            if (!empty($path = realpath(implode(DIRECTORY_SEPARATOR, [
                $basePath, crc32($group) . '_log.dat'
            ])))) {
                $content = @file_get_contents($path);
                if (strlen($content) > 0) {
                    $items = array_merge(
                        $items, unserialize(User::decodeStr(
                            $content
                        ))
                    );
                }
            }
        }
        return $items ?? [];
    }
}
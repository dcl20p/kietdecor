<?php

declare(strict_types=1);

namespace Upload\Controller;

use Laminas\Http\PhpEnvironment\Response;
use Laminas\View\Model\JsonModel;
use \Zf\Ext\Controller\ZfController;
use Laminas\View\Model\ViewModel;

class IndexController extends ZfController
{
    /**
     * List action
     *
     * @return ViewModel
     */
    public function uploadAction(): JsonModel
    {
        try {
            dd($this->getParamsFiles());
            
        } catch (\Throwable $e) {
            $this->saveErrorLog($e);
        }

        return new JsonModel([
            'success' => true
        ]);
    }
}

<?php

namespace App\Controllers;

class Admins extends BaseController
{
    public function index($request, $response, $args)
    {
        enqueueScriptFooter(assets("vendor/jquery-validate/jquery.validate.min.js"));
        enqueueScriptFooter(assets("vendor/datepicker/datepicker.min.js"));
        enqueueScriptFooter(assets("vendor/datatable/datatables.js"));

        enqueueStyleHeader(assets("vendor/datepicker/datepicker.min.css"));
        enqueueStyleHeader(assets("vendor/datatable/datatables.css"));

        $viewData = [];
        $viewData["pageTitle"]      = "کاربران مدیر";
        $viewData["viewContent"]    = "admins/index.php";
        $viewData["activeMenu"]     = "admins";
        $viewData["activePage"]     = "admins";
        $this->render($viewData);
    }

    public function ajaxViewAdd($request, $response, $args)
    {
        $viewData = [];
        $viewData['viewContent']   = 'admins/form.php';
        return $this->renderAjxView($viewData);
    }

    public function ajaxViewEdit($request, $response, $args)
    {
        $editId     = $args["id"];
        $viewData   = [];

        $aModel     = new \App\Models\Admins();
        $userInfo   = $aModel->getInfo($editId);

        if ($userInfo) {
            $viewData['userValues']    = $userInfo;
            $viewData['viewContent']   = 'admins/form.php';
            return $this->renderAjxView($viewData);
        } else {
            $viewData['viewContent']   = 'notfound-modal.php';
        }
        return $this->renderAjxView($viewData);
    }

    public function ajaxAddUser($request, $response, $args)
    {
        $uid       = $request->getAttribute('uid');
        $pdata     = $request->getParsedBody();
        $result    = \App\Validations\Admins::save($pdata);
        if ($result["status"] == "error") {
            return $response->withStatus(400)->withJson($result);
        }

        $aModel = new \App\Models\Admins();
        try {
            $aModel->saveUsers($pdata);
        } catch (\Exception $err) {
            $result["status"] = "error";
            $result["messages"] = "در افزودن کاربر خطایی رخ داد. لطفا دوباره امتحان کنید";
            return $response->withStatus(400)->withJson($result);
        }
    }

    public function ajaxEditUser($request, $response, $args)
    {
        $editId    = $args["id"];
        $uid       = $request->getAttribute('uid');
        $pdata     = $request->getParsedBody();
        $result    = \App\Validations\Admins::save($pdata, $editId);
        if ($result["status"] == "error") {
            return $response->withStatus(400)->withJson($result);
        }

        $aModel = new \App\Models\Admins();
        try {
            $aModel->saveUsers($pdata, $editId);
        } catch (\Exception $err) {
            $result["status"] = "error";
            $result["messages"] = "در افزودن کاربر خطایی رخ داد. لطفا دوباره امتحان کنید";
            return $response->withStatus(400)->withJson($result);
        }
    }

    public function ajaxDeleteUser($request, $response, $args)
    {
        $editId    = $args["id"];
        $uid       = $request->getAttribute('uid');
        $result    = \App\Validations\Admins::delete($editId, $uid);
        if ($result["status"] == "error") {
            return $response->withStatus(400)->withJson($result);
        }

        $aModel = new \App\Models\Admins();
        $aModel->deleteUser($editId, $uid);
    }

    public function ajaxUsersList($request, $response, $args)
    {
        $pdata    = $request->getParsedBody();
        $aModel   = new \App\Models\Admins($this);
        $uid      = $request->getAttribute('uid');

        $result   = $aModel->dataTableList($pdata, $uid);
        return $response->withStatus(200)->withJson($result);
    }
}

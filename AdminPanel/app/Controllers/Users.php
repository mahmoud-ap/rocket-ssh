<?php

namespace App\Controllers;

use \App\Libraries\UserShell;

class Users extends BaseController
{


    public function logout($request, $response, $args)
    {
        container()->session->delete('admin_login');
        container()->session->delete('userInfo');

        return $response->withHeader('Location', baseUrl('login'));
    }

    public function notFoundPage($request, $response)
    {
        enqueueStyleHeader(assets("css/404.css"));

        $response = new \Slim\Http\Response(404);
        $this->setResponse($response);
        $this->templateName = "layouts/404-layout.php";
        $viewData = array();
        $viewData["pageTitle"] = "404";
        return $this->render($viewData);
    }


    public function index($request, $response, $args)
    {
        enqueueScriptFooter(assets("vendor/jquery-validate/jquery.validate.min.js"));
        enqueueScriptFooter(assets("vendor/datepicker/datepicker.min.js"));
        enqueueScriptFooter(assets("vendor/datatable/datatables.js"));

        enqueueStyleHeader(assets("vendor/datepicker/datepicker.min.css"));
        enqueueStyleHeader(assets("vendor/datatable/datatables.css"));

        $viewData = [];
        $viewData["pageTitle"]      = "مدیریت کاربران";
        $viewData["viewContent"]    = "users/index.php";
        $viewData["activeMenu"]     = "users";
        $viewData["activePage"]     = "users";
        $this->render($viewData);
    }

    public function online($request, $response, $args)
    {
        enqueueScriptFooter(assets("vendor/jquery-validate/jquery.validate.min.js"));
        enqueueScriptFooter(assets("vendor/datepicker/datepicker.min.js"));
        enqueueScriptFooter(assets("vendor/datatable/datatables.js"));

        enqueueStyleHeader(assets("vendor/datepicker/datepicker.min.css"));
        enqueueStyleHeader(assets("vendor/datatable/datatables.css"));

        $viewData = [];
        $viewData["pageTitle"]      = "کاربران آنلاین";
        $viewData["viewContent"]    = "users/online.php";
        $viewData["activeMenu"]     = "online-users";
        $viewData["activePage"]     = "online-users";
        $viewData["onlineUsers"]    = UserShell::onlineUsers();

        $this->render($viewData);
    }


    public function ajaxViewAdd($request, $response, $args)
    {
        $viewData = [];
        $viewData['viewContent']   = 'users/form.php';
        return $this->renderAjxView($viewData);
    }

    public function ajaxViewEdit($request, $response, $args)
    {
        $editId     = $args["id"];
        $viewData   = [];

        $uModel     = new \App\Models\Users();
        $userInfo   = $uModel->getInfo($editId);

        if ($userInfo) {
            $viewData['userValues']    = $userInfo;
            $viewData['viewContent']   = 'users/form.php';
            $viewData['refrence']      = $request->getQueryParam("ref");
            return $this->renderAjxView($viewData);
        } else {
            $viewData['viewContent']   = 'notfound-modal.php';
        }
        return $this->renderAjxView($viewData);
    }

    public function ajaxViewDetails($request, $response, $args)
    {

        $editId     = $args["id"];
        $viewData   = [];

        $uModel     = new \App\Models\Users();
        $userInfo   = $uModel->getInfo($editId);
        if ($userInfo) {
            $viewData['userValues']    = $userInfo;
            $viewData['viewContent']   = 'users/details.php';
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
        $result    = \App\Validations\Users::save($pdata);
        if ($result["status"] == "error") {
            return $response->withStatus(400)->withJson($result);
        }

        $uModel = new \App\Models\Users();
        try {
            $uModel->saveUsers($pdata, $uid);
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
        $result    = \App\Validations\Users::save($pdata, $editId);
        if ($result["status"] == "error") {
            return $response->withStatus(400)->withJson($result);
        }

        $uModel = new \App\Models\Users();
        try {
            $uModel->saveUsers($pdata, $uid, $editId);
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
        $result    = \App\Validations\Users::hasExist($editId);
        if ($result["status"] == "error") {
            return $response->withStatus(400)->withJson($result);
        }

        $uModel = new \App\Models\Users();
        $uModel->deleteUser($editId, $uid);
    }

    public function ajaxUsersList($request, $response, $args)
    {
        $pdata    = $request->getParsedBody();
        $uModel   = new \App\Models\Users($this);
        $uid      = $request->getAttribute('uid');

        $result   = $uModel->dataTableList($pdata, $uid);
        return $response->withStatus(200)->withJson($result);
    }


    public function ajaxToggleUserActive($request, $response, $args)
    {
        $editId    = $args["id"];
        $uid       = $request->getAttribute('uid');
        $result    = \App\Validations\Users::hasExist($editId);
        if ($result["status"] == "error") {
            return $response->withStatus(400)->withJson($result);
        }

        $uModel = new \App\Models\Users();
        $uModel->toggleActive($editId, $uid);
    }

    public function ajaxResetUserTraffic($request, $response, $args)
    {
        $editId    = $args["id"];
        $uid       = $request->getAttribute('uid');
        $result    = \App\Validations\Users::hasExist($editId);
        if ($result["status"] == "error") {
            return $response->withStatus(400)->withJson($result);
        }

        $uModel = new \App\Models\Users();
        $uModel->resetTraffic($editId, $uid);
    }

    public function ajaxUsersOnlinesList($request, $response, $args)
    {
        $pdata    = $request->getParsedBody();
        $uModel   = new \App\Models\Users($this);
        $uid      = $request->getAttribute('uid');

        $result   = $uModel->dataTableList($pdata, $uid);
        return $response->withStatus(200)->withJson($result);
    }

    public function ajaxKillPidUsers($request, $response, $args)
    {
        $pdata    = $request->getParsedBody();
        if (!empty($pdata["pids"]) && is_array($pdata["pids"])) {
            $pids = $pdata["pids"];
            UserShell::killUsers($pids);
        }
    }
}

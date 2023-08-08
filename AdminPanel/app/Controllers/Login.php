<?php

namespace App\Controllers;

class Login extends BaseController
{

    public function index($request, $response, $args)
    {
        if (session()->get("admin_login")) {
            return $response->withStatus(302)->withHeader("location", baseUrl("/dashboard"));
        }
        enqueueScriptFooter(assets("vendor/jquery-validate/jquery.validate.min.js"));

        $this->templateName = "layouts/login-layout.php";

        $viewData = [];
        $viewData["pageTitle"]      = "ورود به پنل";
        $viewData["viewContent"]    = "login.php";
        $viewData["activePage"]     = "login";
        $this->render($viewData);
    }

    public function ajaxLogin($request, $response, $args)
    {
        $pdata      = $request->getParsedBody();
        $validate   = \App\Validations\Admins::login($pdata);
        if ($validate["status"] == "error") {
            return $response->withStatus(400)->withJson($validate);
        }

        $aModel   = new \App\Models\Admins($this);
        $result   = $aModel->checkAdminLogin($pdata);
        if ($result) {

            $userId       = $result->id;
            $sessionName  = "admin_login";

            session()->set($sessionName, $userId);

            if (!empty($pdata['remember'])) {
                $sessName = getConfig("session")["name"];
                setcookie(
                    $sessName,
                    container()->session::id(),
                    time() + 30 * 86400,
                    '/'
                );
            }
            $result["next_url"] = baseUrl("dashboard");
            return $response->withStatus(200)->withJson($result);
        }

        return $response->withStatus(404);
    }
}

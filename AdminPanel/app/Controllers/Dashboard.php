<?php

namespace App\Controllers;

use \App\Libraries\UserShell;


class Dashboard extends BaseController
{

    public function index($request, $response, $args)
    {
        $viewData = [];
        $viewData["pageTitle"]      = "داشبورد";
        $viewData["activePage"]     = "dashboard";
        $viewData["viewContent"]    = "dashboard.php";

        enqueueScriptFooter(assets("vendor/datatable/datatables.js"));
        enqueueStyleHeader(assets("vendor/datatable/datatables.css"));

        $uModel = new \App\Models\Users();
        $tModel = new \App\Models\Traffics();


        $totalActiveUsers   = $uModel->totalUsers("active");
        $totalInActiveUsers = $uModel->totalUsers("in_active");

        $viewData["totalData"] = [
            "users" => [
                "all"       => $totalActiveUsers + $totalInActiveUsers,
                "active"    => $totalActiveUsers,
                "inActive"  => $totalInActiveUsers,
                "online"    => UserShell::totalOnlineUsers(),
            ],

        ];
        $viewData["ramData"]        = UserShell::ramData();
        $viewData["cpuData"]        = UserShell::cpuData();
        $viewData["diskData"]       = UserShell::diskData();
        $viewData["serverTraffic"]  = UserShell::serverTraffic();
        $viewData["userTraffic"]    = $tModel->totalData();

        $this->render($viewData);
    }
}

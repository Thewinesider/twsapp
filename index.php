<!DOCTYPE html>
<html lang="en" ng-app="twsApp">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=1" />
        <meta name="apple-mobile-web-app-capable" content="yes" />
        <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent" />
        <title>The Winesider</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css">   
        <link href="stylesheets/style.css" rel="stylesheet">
        <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
        <link rel="stylesheet" href="stylesheets/angular-datatables.css">
        <link rel="stylesheet" href="stylesheets/daterangepicker.css"/>
        <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css">
        <link rel="stylesheet" href="stylesheets/jquery.dataTables.min.css">
        <link rel="icon" sizes="192x192" href="img/icon-highres.png">
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]><link href= "css/bootstrap-theme.css" rel= "stylesheet" >
<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
<script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
<![endif]-->
    </head>

    <body id="wrapper" class="gray-bg mini-navbar" ng-cloak="">
        <nav class="navbar-default navbar-static-side" role="navigation" ng-show="authenticated">
            <div class="sidebar-collapse">
                <ul class="nav metismenu" id="side-menu">
                    <li class="nav-header">
                        <div class="logo-pittogramma white"></div>
                        <div class="logo-element">
                            TWS 
                        </div>
                    </li>
                    <li>
                        <a><i class="fa fa-bar-chart"></i><span class="nav-label">Statistiche</span> </a>
                        <ul class="nav nav-second-level collapse">
                            <li><a ng-href="#/dashboard"><i class="fa fa-bar-chart"></i> Statistiche</a></li>
                            <li><a ng-href="#/admin"><i class="fa fa-list-ul"></i> Dettaglio</a></li>
                        </ul>
                    </li>
                    <li>
                        <a><i class="fa fa-book"></i> <span class="nav-label">Il nostro catalogo</span> </a>
                        <ul class="nav nav-second-level collapse">
                            <li><a ng-href="#/catalog-list"><i class="fa fa-list"></i> Lista vini</a></li>
                            <li><a ng-href="#/catalog-grid"><i class="fa fa-th"></i> Griglia vini</a></li>
                            <!--li><a ng-href="#/catalog-grid"><i class="fa fa-pie-chart"></i> Overview vini</a></li-->
                        </ul>
                    </li>
                    <li>
                        <a><i class="fa fa-tablet"></i> <span class="nav-label">WineSpy</span></a>
                        <ul class="nav nav-second-level collapse">
                            <li><a ng-href="#/winespy"><i class="fa fa-tablet"></i> Scarica venduto</a></li>
                            <li><a ng-href="#/lastwine"><i class="fa fa-clock-o"></i> Vini scaricati</a></li>
                        </ul>
                    </li>
                    <li>
                        <a ng-click="logout()"><i class="fa fa-sign-out"></i><span class="nav-label"> Log out</span></a>
                    </li>
                </ul>
            </div>
        </nav>

        <div data-ng-view="" id="ng-view" class="tws-color"></div>

    </body>

    <toaster-container toaster-options="{'time-out': 3000}"></toaster-container>

    <!-- Jquery  -->
    <script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.5/angular.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.5/angular-animate.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.5/angular-touch.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.5/angular-route.min.js"></script>
    <script src="js/ui-bootstrap-tpls-2.2.0.min.js"></script>
    <script async src="js/chart.min.js"></script>
    <script async src="js/underscore.min.js"></script>
    <script type="text/javascript" src="js/chartjs-directive.js"></script>
    <!-- moment.js -->
    <script src="js/moment/moment.js"></script>
    <script src="js/angular-moment/angular-moment.js"></script>
    <!-- daterangepicker.js -->
    <script src="js/daterangepicker.js"></script>
    <script src="js/angular-daterangepicker.min.js"></script>
    <!-- daterangepicker.js -->
    <script src="js/datatables/jquery.dataTables.min.js"></script>
    <script src="js/datatables/angular-datatables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.2.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.2.2/js/buttons.colVis.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.2.2/js/buttons.flash.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.2.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.2.2/js/buttons.print.min.js"></script>
    <script src="js/datatables/angular-datatables.buttons.min.js"></script>
    <!-- Toastr -->
    <script src="js/toaster.js"></script>
    <!-- ANGULAR -->
    <script src="app/app.js"></script>
    <script src="app/factory.js"></script>
    <script src="app/directives.js"></script>
    <script src="app/controller.js"></script>
    <script src="app/functions.js"></script>
</html>


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
                    <li class="active">
                        <a ng-href="#/dashboard" ng-show="role == 'admin'"><i class="fa fa-line-chart"></i> <span class="nav-label">Statistiche</span> </a>
                        <a ng-href="#/catalog-list" ng-show="role == 'admin'"><i class="fa fa-list-ol"></i> <span class="nav-label">Il nostro catalogo</span> </a>
                        <a ng-href="#/winespy"><i class="fa fa-arrow-circle-down"></i> <span class="nav-label">WineSpy</span></a>
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
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.5/angular-messages.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.5/angular-touch.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.5/angular-route.min.js"></script>
    <script src="app/js/ui-bootstrap-tpls-2.2.0.min.js"></script>
    <script async src="app/js/plugins/slimscroll/jquery.slimscroll.min.js"></script>
    <script async src="app/js/chart.min.js"></script>
    <script async src="app/js/underscore.min.js"></script>
    <script type="text/javascript" src="app/js/chartjs-directive.js"></script>
    <script src="app/js/node_modules/moment/moment.js"></script>
    <script src="app/js/node_modules/angular-moment/angular-moment.js"></script>
    <!-- Toastr -->
    <script src="app/js/toaster.js"></script>
    <script src="app/app.js"></script>
    <script src="app/data.js"></script>
    <script src="app/directives.js"></script>
    <script src="app/controller.js"></script>
</html>


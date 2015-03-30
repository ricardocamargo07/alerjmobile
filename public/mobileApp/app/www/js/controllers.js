angular.module('starter.controllers', [])

    .controller('AppCtrl', function($scope, $ionicModal, $timeout) {
        // Form data for the login modal
        $scope.loginData = {};

        // Create the login modal that we will use later
        $ionicModal.fromTemplateUrl('templates/login.html', {
            scope: $scope
        }).then(function(modal) {
            $scope.modal = modal;
        });

        // Triggered in the login modal to close it
        $scope.closeLogin = function()
        {
            $scope.modal.hide();
        };

        // Open the login modal
        $scope.login = function()
        {
            $scope.modal.show();
        };

        // Perform the login action when the user submits the login form
        $scope.doLogin = function()
        {
            console.log('Doing login', $scope.loginData);

            // Simulate a login delay. Remove this and replace with your login
            // code if using a login system
            $timeout(function()
            {
                $scope.closeLogin();
            }, 1000);
        };
    })

    .controller('PagesController', function($scope)
    {
        $scope.pages = [
            { title: 'Plenário Ao Vivo', id: 'plenario' },
            { title: 'Deputados', id: 'deputados' },
            { title: 'Notícias', id: 'noticias' },
            { title: 'Alô, ALERJ!', id: 'alo' },
            { title: 'Comissões Permanentes', id: 'comissoes' },
            { title: 'Regimento Interno', id: 'regimento' },
            { title: 'Diário Oficial', id: 'diario' }
        ];
    })

    .controller('PartiesController', function($scope, $http)
    {
        $http.get('http://alerjapi.antoniocarlosribeiro.com/api/v1.0/parties')
            .then(function(res){
                $scope.parties = res.data;
            });
    })

    .controller('CongressmanController', function($scope, $stateParams, $sce)
    {
        $http.get('http://alerjapi.antoniocarlosribeiro.com/api/v1.0/parties')
            .then(function(res){
                $scope.parties = res.data;
            });

        
        $scope.congressman_page = $stateParams.congressman_name;
    })

    .controller('PageController', function($scope, $stateParams)
    {
    });

apiUrl = 'http://alerjapi.antoniocarlosribeiro.com';
//apiUrl = 'http://api.alerj.com';

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
            { title: 'Ordem do Dia', id: 'ordemDoDia' },
            { title: 'Notícias', id: 'noticias' },
            { title: 'Alô, ALERJ!', id: 'alo' },
            { title: 'Comissões Permanentes', id: 'comissoes' },
            { title: 'Regimento Interno', id: 'documents/Regimento Interno' },
            { title: 'Constituição Estadual', id: 'documents/Constituição Estadual' },
            { title: 'Diário Oficial', id: 'diario' }
        ];
    })

    .controller('PartiesController', function($scope, $http)
    {
        $http.get(apiUrl+'/api/v1.0/parties')
            .then(function(res){
                $scope.parties = res.data;
            });
    })

    .controller('CongressmanController', function($scope, $stateParams, $sce, $http)
    {
        $scope.congressman_name = $stateParams.congressman_name;

        // $http.get('http://alerjapi.antoniocarlosribeiro.com/api/v1.0/congressman/profile/'+$stateParams.congressman_id)
        $http.get(apiUrl+'/api/v1.0/congressman/profile/'+$stateParams.congressman_id)
            .then(function(res){
                $scope.congressman_page = $sce.trustAsHtml(res.data);
            });
    })

    .controller('DocumentsController', function($scope, $http, $stateParams)
    {
        $http.get(apiUrl+'/api/v1.0/documentsPages/'+$stateParams.name)
            .then(function(res){
                $scope.documents = res.data;
                $scope.title = $stateParams.name;
            });

        $scope.clearSearch = function() {
            $scope.data.searchQuery = '';
        };
    })

    .controller('DocumentsPagesController', function($scope, $http, $stateParams)
    {
        $http.get(apiUrl+'/api/v1.0/documentsPages/page/'+$stateParams.page_id)
            .then(function(res){
                $scope.page = res.data;
            });
    })

    .controller('OrdemDoDiaController', function($scope, $http)
    {
        $http.get(apiUrl+'/api/v1.0/schedule')
            .then(function(res){
                $scope.schedule = res.data;
            });
    })

    .controller('OrdemDoDiaItemController', function($scope, $http, $stateParams)
    {
        $http.get(apiUrl+'/api/v1.0/schedule/'+$stateParams.alerj_id)
            .then(function(res){
                $scope.item = res.data;
            });
    })

    .controller('PageController', function($scope, $stateParams)
    {
    });

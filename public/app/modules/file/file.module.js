(function(){
  angular
    .module('file',[])
    .config(config);

    function config($routeProvider){
      $routeProvider
        .when('/files',{
        controller: 'File',
        controllerAs: 'vm',
        templateUrl: 'public/app/modules/file/file.html'
      }).when('/files/:id', {
        controller: 'File',
        controllerAs: 'vm',
        templateUrl: 'public/app/modules/file/detail.html'
      });
    }
})();

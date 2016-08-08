(function(){
   angular
   .module('home',[])
   .config(config);

   function config($routeProvider){
     $routeProvider
      .when('/',{
         controller: 'Home',
         controllerAs: 'vm',
         templateUrl: 'public/app/modules/home/home.html'
      }).when('/files/:id', {
        controller: 'Home',
        controllerAs: 'vm',
        templateUrl: 'public/app/modules/home/file.html'
      }).when('/files', {
        controller: 'Home',
        controllerAs: 'vm',
        templateUrl: 'public/app/modules/home/file.html'
      });
   }
})();

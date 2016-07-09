(function(){
  angular
    .module('home')
    .factory('homeService', homeService);

    homeService.$inject = ['$http'];

    function homeService($http){
      var service = {
          setFormDefaults: setFormDefaults,
          setViewDefaults: setViewDefaults
      };

      return service;

      function setFormDefaults(){
        var file = {
          createdBy: "1",
          parish: "JM-01",
          status: "0",
          currency: "JMD",
          receipt: "no"
        };
        return file;
      }

      function setViewDefaults(){
        var view = {
          hideControls: false,
          message: false,
          create_view: true,
          file_view: false
        };
        return view;
      }
    }
})();

(function(){
  angular
    .module('home')
    .factory('homeService', homeService);

    homeService.$inject = ['$http'];

    function homeService($http){
      var service = {
          createFile: createFile,
          setFormDefaults: setFormDefaults
      };

      return service;
      /**
       * Creates a file
       * @param  {[type]} file File record object
       * @return {[type]}      [description]
       */
      function createFile(file){
        return $http.post('/api/v1/files', file)
                    .then(handleSuccess)
                    .catch(handleError);
        function handleSuccess(response){ return response.data; }
        function handleError(error){ return error; }
      }

      function setFormDefaults(){
        var file = {
          createdBy: "1",
          parish: "JM-01",
          activity: "0",
          currency: "JMD",
          receipt: "no"
        };
        return file;
      }
    }
})();

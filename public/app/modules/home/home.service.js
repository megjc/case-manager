(function(){
  angular
    .module('home')
    .factory('homeService', homeService);

    homeService.$inject = ['$http'];

    function homeService($http){
      var service = {
          createFile: createFile,
          setFormDefaults: setFormDefaults,
          getUsersByType: getUsersByType,
          getSystemLists: getSystemLists,
          getFiles: getFiles
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
      /**
       * Retrieves all data entry users from the database
       * @return {[type]} [description]
       */
      function getUsersByType(){
        return $http.get('/api/v1/users?type=2')
                    .then(handleSuccess)
                    .catch(handleError);
        function handleSuccess(response){ return response.data; }
        function handleError(error){ return error; }
      }
      /**
       * Retrieve all file activity types
       * @return {[type]} [description]
       */
      function getSystemLists(){
        return $http.get('/api/v1/sys_lists')
                    .then(handleSuccess)
                    .catch(handleError);
        function handleSuccess(response){ return response.data; }
        function handleError(error){ return error; }
      }

      function setFormDefaults(){
        var file = {
          receipt: "no"
        };
        return file;
      }
      /**
       * Retrieves all files 
       * @return {[type]} [description]
       */
      function getFiles(){
        return $http.get('/api/v1/files')
                    .then(handleSuccess)
                    .catch(handleError);
        function handleSuccess(response){ return response.data; }
        function handleError(error){ return error; }
      }
    }
})();

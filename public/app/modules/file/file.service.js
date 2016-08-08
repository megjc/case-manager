(function(){
  angular
    .module('file')
    .factory('fileService', fileService);

    fileService.$inject = ['$http'];

    function fileService($http){
      var service = {
        getFiles: getFiles,
        getFileById: getFileById
      };

      return service;
      /**
       * Retrieves all files
       * @return {[type]} [description]
       */
      function getFiles(){
        return $http.get('/case-manager/api/v1/files')
                    .then(handleSuccess)
                    .catch(handleError);
        function handleSuccess(response){ return response.data; }
        function handleError(error){ return error; }
      }

      function getFileById(id){
        return $http.get('/case-manager/api/v1/files/' + id)
                    .then(handleSuccess)
                    .catch(handleError);
        function handleSuccess(response){ return response.data; }
        function handleError(response){ return error; }
      }
    }
})();

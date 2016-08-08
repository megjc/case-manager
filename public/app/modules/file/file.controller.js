(function(){
  angular
  .module('file')
  .controller('File', File);

  File.$inject = ['fileService', '$location', '$routeParams'];

  function File(fileService, $location, $routeParams){
    var vm = this;
  //  vm.showFileDetail = showFileDetail;
    vm.file = {};

    fileService.getFiles().then(function(files){
      vm.files = files;
    }).catch(function(error){
      vm.files = [];
    });

    //function showFileDetail(id){
      fileService.getFileById($routeParams.id).then(function(file){
        vm.file = file;
      }).catch(function(error){
        vm.file = {};
      });
    //}
  }
})();

(function(){
    angular
    .module('home')
    .controller('Home', Home);

    function Home(){
        var vm = this;
        vm.file = {};
        vm.addOwner = addOwner;
        vm.removeOwner = removeOwner;
        vm.showControls = showControls;
        vm.hideControls = false;
        vm.file.receipt = "no";
        vm.ownerList = [];
        vm.volumeAndFolioList = [];
        vm.selectedUser = "";
        vm.message = true;
        vm.dismiss = dismiss;
        vm.addPair = addPair;
        vm.create_view = true;
        vm.file_view = false;
        vm.show = show;
        /**
         * Adds a property owner to a list
         */
        function addOwner(){
          var name = "none";
          if(typeof vm.owner !== 'undefined'){
             if(vm.owner.length > 0) name = vm.owner;
          }
          if(!ownerExists(name)) vm.ownerList.push(name);
          vm.owner = "";
        }
        /**
         * Removes a property owner by name
         * @param  {[type]} owner Name of property owner
         * @return {[type]}       [description]
         */
        function removeOwner(owner){
          var len = vm.ownerList.length;
          while(len--){
            if(vm.ownerList[len] === owner) {
              removed = vm.ownerList.splice(len, 1);
            }
          }
        }
        /**
         * Adds a volume and folio number to a list
         */
        function addPair(){
          console.log(vm.file.volume);
          console.log(vm.file.folio);
        }
        /**
         * Removes a property owner by name
         * @param  {[type]} owner Name of property owner
         * @return {[type]}       [description]
         */
        function removePair(owner){
          var len = vm.ownerList.length;
          while(len--){
            if(vm.ownerList[len] === owner) {
              removed = vm.ownerList.splice(len, 1);
            }
          }
        }

        /**
         * Determines if a owner already exists in the list
         * @param  {[type]} owner Name of owner
         * @return {[type]}       [description]
         */
        function ownerExists(owner){
          var found = false;
          var len = vm.ownerList.length;
          while(len--){
            if(vm.ownerList[len] === owner) {
              found = true;
            }
          }
          return found;
        }
        /**
         * Displays currency form controls
         * @return {[type]} [description]
         */
        function showControls(){
           if(vm.file.receipt === "yes") vm.hideControls = true;
           else vm.hideControls = false;
        }
        /**
         * Dismisses success or failure message
         * @return {[type]} [description]
         */
        function dismiss(){
          vm.message = false;
        }
        /**
         * Toggle views
         * @param  {[type]} view Name of the view
         * @return {[type]}      [description]
         */
        function show(view){
          if(view === 'create-view'){
             vm.create_view = true;
             vm.file_view = false;
          }else{
            vm.create_view = false;
            vm.file_view = true;
          }
        }
    }
})();

(function(){
    angular
    .module('home')
    .controller('Home', Home);

    Home.$inject = ['homeService'];

    function Home(homeService){
        var vm = this;
        vm.file = {};
        vm.owner = {
          name: "",
          folio: "",
          volume: ""
        };
        /**
         * View settings
         * @type {Boolean}
         */
        vm.hideControls = false;
        vm.ownerList = [];
        vm.message = false;
        vm.error_message = false;
        vm.create_view = true;
        vm.file_view = false;
        /**
         * Controller functions
         */
        vm.show = show;
        vm.remove = remove;
        vm.processForm = processForm;
        vm.add = add;
        vm.showControls = showControls;
        vm.dismiss = dismiss;
        vm.file = homeService.setFormDefaults();
        /**
         * Adds a property owner to a list
         */
        function add(){
          var owner = {name: "not seen", volume: "not seen", folio: "not seen"};
          if(typeof vm.owner.name !== 'undefined'){
             if(vm.owner.name.length > 0)
             owner = {name: vm.owner.name,
                      volume: vm.owner.volume,
                      folio: vm.owner.folio };
          }
          if(!ownerExists(owner)) vm.ownerList.push(owner);
          vm.owner.name = vm.owner.volume = vm.owner.folio = "";
        }
        /**
         * Removes a property owner by name
         * @param  {[type]} owner Name of property owner
         * @return {[type]}       [description]
         */
        function remove(owner){
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
            if(vm.ownerList[len].name === owner.name
              && vm.ownerList[len].volume === owner.volume
              && vm.ownerList[len].folio === owner.folio) {
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
          vm.error_message = false;
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
        /**
         * Creates a file from form input
         * @return {[type]} [description]
         */
        function processForm(){
          //add numeric validation for volume, folio, currency
          if(vm.file.end_date === null
            || vm.file.start_date === null
            || vm.file.title === ""){
              vm.error_message = true;
              return;
          }

          vm.message = true;
          vm.error_message = false;

           homeService.createFile(vm.file).then(function(response){
            console.log(response);
            vm.file = {};
            vm.file = homeService.setFormDefaults();
            vm.ownerList = [];
            vm.message = true;
            vm.hideControls = false;
          }).catch(function(error){
            console.log('Error');
          });
        }
    }
})();

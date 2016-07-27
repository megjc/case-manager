(function(){
    angular
    .module('home')
    .controller('Home', Home);

    Home.$inject = ['homeService'];

    function Home(homeService){
        var vm = this;
        vm.file = {};
        vm.receipt = {
          currency : "jmd",
          seen: "no"
        };
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
        vm.receiptList = [];
        vm.message = false;
        vm.error_message = false;
        vm.create_view = true;
        vm.file_view = false;
        vm.enableAppend = false;
        /**
         * Controller functions
         */
        vm.show = show;
        vm.remove = remove;
        vm.processForm = processForm;
        vm.add = add;
        vm.append = append;
        vm.showControls = showControls;
        vm.dismiss = dismiss;
        vm.file = homeService.setFormDefaults();

        /**
         * Get lists of entities to be used by the form.
         * @return {[type]} [description]
         */
        homeService.getSystemLists().then(function(lists){
          setLists(lists);
        }).catch(function(error){
          vm.users = vm.activities = vm.parishes = vm.currencies = vm.receipt_types = [];
        });
        /**
         * Sets select lists for the form.
         * @param {[type]} lists
         * TODO - move to the homeService
         */
        function setLists(lists){
          vm.users = lists.users;
          vm.file.createdBy = vm.users[0];
          vm.activities = lists.activities;
          vm.file.activity = vm.activities[0];
          vm.parishes = lists.parishes;
          vm.file.parish = vm.parishes[0];
          vm.currencies = lists.currencies;
          vm.file.currency = vm.currencies[0];
          vm.receipt_types = lists.receipt_types;
          vm.receipt.type = vm.receipt_types[0];
        }
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
         * Adds an item to a given list
         * @param  {[type]}   item          [description]
         * @param  {[type]}   list          [description]
         * @param  {[type]}   section_title [description]
         * @param  {Function} cb            [description]
         * @return {[type]}                 [description]
         */
        function append(item, list, section_title){
            var list_item = angular.copy(item);
            if(!itemExists(list_item, list))
                list.push(list_item);

            clearInput(section_title);
        }
        /**
         * Clears a set of inputs for a given section of the form
         * @param  {[type]} section_title [description]
         * @return {[type]}               [description]
         */
        function clearInput(section_title){
          switch (section_title) {
            case 'receipt':
              vm.receipt.currency = vm.receipt.amount = "";
              vm.receipt.seen = "no";
              vm.receipt.currency = "jmd";
              vm.enableAppend = true;
              break;
              case 'owner':
                vm.owner.name = vm.owner.volume = vm.owner.folio = "";
                break;
          }
        }
        /**
         * Checks if an item exists in a given list
         * @param  {[type]} item [description]
         * @param  {[type]} list [description]
         * @return {[type]}      [description]
         */
        function itemExists(item, list){
          var found = false, len = list.length;
          while(len--){
            if(angular.equals(list[len], item)) found = true;
          }
          return found;
        }
        /**
         * Removes an item from a list
         * @param  {[type]} owner Name of property owner
         * @return {[type]}       [description]
         */
        function remove(item, list){
          var len = list.length;
          while(len--){
            if(list[len] === item) {
              removed = list.splice(len, 1);
            }
          }
          return removed;
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
           if(vm.receipt.seen === "yes") {
             vm.hideControls = true;
             vm.enableAppend = false;
           }else{
             vm.hideControls = false;
             vm.enableAppend = true;
           }


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
          vm.file.owners = vm.ownerList;
          /**
           * [createFile description]
           * @param  {[type]} vm.file [description]
           * @return {[type]}         [description]
           */
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

(function(){
    angular
    .module('home')
    .controller('Home', Home);

    Home.$inject = ['homeService', '$window'];

    function Home(homeService, $window){
        var vm = this;
        vm.acc_id = 0;
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
        function getLists(){
          homeService.getSystemLists().then(function(lists){
            setLists(lists);
          }).catch(function(error){
            vm.users = vm.activities = vm.parishes = vm.currencies = vm.receipt_types = [];
          });
        }

        getLists();

        /**
         * Sets select lists for the form.
         * @param {[type]} lists
         * TODO - move to the homeService
         */
        function setLists(lists){
          vm.users = lists.users;
          vm.file.createdBy = vm.users[0].user_id;
          vm.activities = lists.activities;
          vm.file.activity = vm.activities[1].id;
          vm.parishes = lists.parishes;
          vm.file.parish = vm.parishes[0].id;
          vm.currencies = lists.currencies;
          vm.receipt.currency = vm.currencies[0].id;
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
            getFiles();
          }
        }
        /**
         * Creates a file from form input
         * @return {[type]} [description]
         */
        function processForm(){
          /**
           * TODO - add numeric validation for volume, folio, currency
           */
          if(vm.file.end_date === null
            || vm.file.start_date === null
            || vm.file.title === ""){
              vm.error_message = true;
              return;
          }

          vm.message = true;
          vm.error_message = false;
          vm.file.owners = vm.ownerList;
          vm.file.receipts = vm.receiptList;

          if( vm.file.property_title === undefined
                || vm.file.property_title === "") vm.file.property_title = "none seen";

          if(vm.file.remarks === undefined || vm.file.remarks === "")
              vm.file.remarks = "none seen";

          if(vm.file.file_id === "" || vm.file.file_id === undefined)
              vm.file.file_id = "none seen";

          if(vm.file.lease_agreement === undefined)
              vm.file.lease_agreement = 0;

          if(vm.file.comp_agreement === undefined)
              vm.file.comp_agreement = 0;

          if(vm.file.cot === undefined) vm.file.cot = 0;
          if(vm.file.map === undefined) vm.file.map = 0;
          if(vm.file.sale_agreement === undefined)
             vm.file.sale_agreement = 0;

          if(vm.file.surveyor_drawing === undefined)
             vm.file.surveyor_drawing = 0;

          if(vm.file.surveyor_report === undefined)
            vm.file.surveyor_report = 0;

          //console.log(vm.file);
           homeService.createFile(vm.file).then(function(response){
            vm.acc_id = response;
            resetForm();
            $window.scrollTo(0,0);
          }).catch(function(error){
            console.log('Error');
          });
        }
        /**
         * Retrieve all files for view
         * @return {[type]} [description]
         */
        function getFiles(){
          homeService.getFiles().then(function(files){
            vm.files = files;
          }).catch(function(error){
            vm.files = [];
          });
        }
        /**
         * Resets form
         */
        function resetForm(){
          vm.file = {};
          vm.file = homeService.setFormDefaults();
          vm.ownerList = [];
          vm.receiptList = [];
          vm.message = true;
          vm.hideControls = false;
          getLists();
        }
    }
})();

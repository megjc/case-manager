(function(){
    'use strict';
    angular
        .module('case-manager')
        .filter('timestamp', function(){
		          return function(dateString){
			             var dateObject = new Date(dateString).toISOString();
			                return dateObject;
		          }
        }).filter('convert', function(){
            return function(value){
              var convertedValue = "";
              if(value === "0") convertedValue = "none seen"
              else convertedValue = "seen";
              return convertedValue;
            }
        });
})();

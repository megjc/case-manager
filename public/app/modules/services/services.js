(function(){
  angular
      .module('shared-services',[])
      .service('requestInterceptor', requestInterceptor);

      requestInterceptor.$inject = ['$q', '$rootScope', '$log'];

      function requestInterceptor($q, $rootScope, $log){
        var interceptor = {
          request: request,
          requestError: requestError,
          response: response,
          responseError: responseError
        };

        return interceptor;

        function request(config){
          return config;
        }

        function requestError(rejection){
          $rootScope.showError = true;
          return $q.reject(rejection);
        }

        function response(response){
          return response;
        }

        function responseError(rejection){
          $rootScope.showError = true;
          return $q.reject(rejection);
        }
      }
})();

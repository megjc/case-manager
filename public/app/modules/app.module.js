(function(){
  angular
  .module('case-manager',[
      'ngRoute',
      'ngMessages',
      'home',
      'shared-services'
  ]).config(config);
  /**
   * Intercepts application requests and server responses 
   * @param  {[type]} $httpProvider [description]
   * @return {[type]}               [description]
   */
  function config($httpProvider){
    $httpProvider.interceptors.push('requestInterceptor');
  }
})();

(function(){
  angular
  .module('case-manager',[
      'ngRoute',
      'ngMessages',
      'home',
      'file',
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

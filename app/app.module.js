angular.module('app',['ngRoute','perfect_scrollbar']);

angular.module('app').directive('ngEnter', function () {
    return function (scope, element, attrs) {
        element.bind("keydown keypress", function (event) {
            if (event.which === 13) {
                scope.$apply(function () {
                    scope.$eval(attrs.ngEnter);
                });
                event.preventDefault();
            }
        });
    };
})
.run(function ($rootScope, $location, $route, playerService, authService) {
    $rootScope.config = {};
    $rootScope.config.app_url = $location.url();
    $rootScope.config.app_path = $location.path();
    $rootScope.layout = {};
    $rootScope.layout.loading = false;

    $rootScope.$on('$routeChangeStart', function () {
        $('.main-menu .menu-item.active').removeClass('active');
        authService.currentList = null;
        $('#loading').show();
        $('ng-view').hide();
        $('.middle-view').scrollTop(0);
    });
    $rootScope.$on('$routeChangeSuccess', function () {
        $('#loading').hide();
        $('ng-view').show();
        $('.middle-view').scrollTop(0);
    });
    $rootScope.$on('$routeChangeError', function () {
    });

    if (localStorage.getItem("playing") !== null) {
        playerService.playing = JSON.parse(localStorage.getItem("playing"));
        playerService.queue = JSON.parse(localStorage.getItem("queue"));
        playerService.originalQueue = JSON.parse(localStorage.getItem("queue"));
        playerService.playing.playing = false;
        playerService.playing.shuffling = false;
        //playerService.volume = localStorage.getItem("volume");
    }
});
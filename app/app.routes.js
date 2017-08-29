angular.module('app').config(function($routeProvider) {
	$routeProvider
		.when('/home', {
			templateUrl: 'app/templates/home.html',
			controller: 'homeController'
		})
        .when('/search/:query', {
            templateUrl: 'app/templates/search.html',
            controller: 'searchController',
            resolve: {
                results: function($http, $route) {
                    return $.get('api/?q=search&p='+$route.current.params.query, function(data) {
                        return data;
                    })
                }
            }
        })
        .when('/top-50', {
            templateUrl: 'app/templates/top-50.html',
            controller: 'top50Controller',
            resolve: {
                list: function($http, $route) {
                    return $.get('api/?q=topTracks', function(data) {
                        return data;
                    })
                }
            }
        })
		.otherwise({
			redirectTo: '/home'
		});
});

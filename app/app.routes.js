angular.module('app').config(function($routeProvider) {
	$routeProvider
        .when('/album/:id', {
            templateUrl: 'app/templates/album.html',
            controller: 'albumController',
            resolve: {
                results: function($http, $route) {
                    return $.get('api/?q=album&id='+$route.current.params.id, function(data) {
                        return data;
                    })
                }
            }
        })
        .when('/artist/:id', {
            templateUrl: 'app/templates/artist.html',
            controller: 'artistController',
            resolve: {
                artist: function($http, $route) {
                    return $.get('api/?q=artist&id='+$route.current.params.id, function(data) {
                        return data;
                    })
                },
                albums: function($http, $route) {
                    return $.get('api/?q=artistAlbums&id='+$route.current.params.id, function(data) {
                        return data;
                    })
                },
                tracks: function($http, $route) {
                    return $.get('api/?q=artistTopTracks&id='+$route.current.params.id, function(data) {
                        return data;
                    })
                },
                related: function($http, $route) {
                    return $.get('api/?q=artistRelatedArtists&id='+$route.current.params.id, function(data) {
                        return data;
                    })
                }
            }
        })
		.when('/home', {
			templateUrl: 'app/templates/home.html',
			controller: 'homeController'
		})
        .when('/login', {
            templateUrl: 'app/templates/login.html',
            controller: 'loginController'
        })
        .when('/new-releases', {
            templateUrl: 'app/templates/new-releases.html',
            controller: 'newReleasesController',
            resolve: {
                results: function($http, $route) {
                    return $.get('api/?q=newReleases', function(data) {
                        return data;
                    })
                }
            }
        })
        .when('/playlist/:id', {
            templateUrl: 'app/templates/playlist.html',
            controller: 'playlistController',
            resolve: {
                tracks: function($http, $route) {
                    return $.post('api/', {
                        'q':'getPlaylistTracks',
                        'token':localStorage.getItem("token"),
                        'playlistId':$route.current.params.id
                    }, function(data) {
                        return data;
                    }).fail(function() {
                        window.location.href = "#!/401";
                    });
                },
                info: function($http, $route) {
                    return $.post('api/', {
                        'q':'getPlaylistInfo',
                        'token':localStorage.getItem("token"),
                        'playlistId':$route.current.params.id
                    }, function(data) {
                        return data;
                    }).fail(function() {
                        window.location.href = "#!/401";
                    });
                }
            }
        })
        .when('/register', {
            templateUrl: 'app/templates/register.html',
            controller: 'registerController'
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

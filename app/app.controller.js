angular.module('app').controller('appController', ['$scope', 'playerService', 'authService', '$window', function($scope, playerService, authService, $window) {
    $scope.player = playerService;
    $scope.auth = authService;

    $scope.route = function(destination) {
        $window.location.href = "#!/"+destination;
    }

    $scope.seekVideo = function(e) {
        var newTime = player.getDuration() * (e.offsetX / $(e.currentTarget).width());
        player.seekTo(newTime);
    }

    $scope.changeVolume = function(e) {
        var newVolume = 100 * (e.offsetX / $(e.currentTarget).width());
        player.setVolume(newVolume);
        setTimeout(updateVolumeBar, 30);
        localStorage.volume = newVolume;
    }

    $scope.muteVolume = function() {
        if (player.isMuted()) {
            player.unMute();
            $('.volume-container .volume').html('volume_up');
        } else {
            player.mute();
            $('.volume-container .volume').html('volume_off');
        }
    }

    $scope.search = function(query) {
        $window.location.href = '#!/search/'+query;
    }

    $scope.logout = function() {
        localStorage.removeItem("token");
        authService.user = null;
        authService.playlists = null;
    }

    $scope.saveToPlaylist = function() {
        var uris = [];
        for (var i = 0; i < $scope.player.queue.length; i++) {
            uris.push($scope.player.queue[i].id);
        }
        var name = prompt("Please enter the playlist's name:", "New Playlist");
        $.ajax({
                type:"POST",
                url:"api/",
                data:{
                    'q':'createPlaylist',
                    'token':localStorage.getItem("token"),
                    'name':name
                },
                success: function(response) {
                    $.ajax({
                        type:"POST",
                        url:"api/",
                        data:{
                            'q':'addPlaylistSongs',
                            'token':localStorage.getItem("token"),
                            'playlistId':response.id,
                            'songs':uris
                        },
                        success: function(response) {
                            $window.location.href = "#!/playlist/"+response.id;
                        },
                        error: function() {
                            alert("An error occured!");
                        },
                        async: false
                    });
                },
                error: function() {
                    alert("An error occured!");
                },
                async: false
            });
        authService.playlists = null;
    }
}]);

angular.module('app').controller('albumController', ['$scope', 'results', function($scope, results) {
    $scope.album = results;
    $scope.totalDuration = 0;
    for (var i = 0; i < $scope.album.tracks.items.length; i++) {
        $scope.totalDuration += $scope.album.tracks.items[i].duration_ms;
        $scope.album.tracks.items[i].rank = i+1;
    }
}]);

angular.module('app').controller('artistController', ['$scope', 'artist', 'albums', 'tracks', 'related', function($scope, artist, albums, tracks, related) {
    $scope.artist = artist;
    $scope.tracks = tracks.tracks;
    $scope.artists = related.artists;
    $scope.albums = albums.items;
}]);

angular.module('app').controller('loginController', ['$scope', '$window', function($scope, $window) {
    $scope.email = "";
    $scope.pass = "";

    if (localStorage.getItem("token") !== null) {
        $window.location.href = "#!/";
    }

    $scope.login = function() {
        if ($scope.pass.length > 0 && $scope.email.length > 0) {
            $.post("api/", {
                'q':'login',
                'email':$scope.email,
                'password':$scope.pass
            }, function(data) {
                localStorage.token = data.token;
                $window.location.href = "#!/";
            }).fail(function(response) {
                $('#login-page .form .info').text(response.responseText);
            });
        }
    }
}]);

angular.module('app').controller('newReleasesController', ['$scope', 'results', function($scope, results) {
    $('.main-menu .menu-item#new-releases').addClass('active');
    $scope.albums = results.albums;
}]);

angular.module('app').controller('playlistController', ['$scope', 'tracks', 'info', 'authService', function($scope, tracks, info, authService) {
    $scope.tracks = tracks.tracks;
    $scope.info = info;
    authService.currentList = info.id;

    for (var i = 0; i < $scope.tracks.length; i++) {
        $scope.tracks[i].rank = i+1;
    }

    $scope.totalDuration = function() {
        var length_ms = 0;
        for (var i = 0; i < $scope.tracks.length; i++) {
            length_ms += $scope.tracks[i].duration_ms;
        }
        return length_ms;
    }
}]);

angular.module('app').controller('registerController', ['$scope', '$window', function($scope, $window) {
    $scope.firstName = "";
    $scope.lastName = "";
    $scope.email = "";
    $scope.pass = "";
    $scope.passConf = "";

    if (localStorage.getItem("token") !== null) {
        $window.location.href = "#!/";
    }

    $scope.register = function() {
        if ($scope.checkName($scope.firstName) &&
            $scope.checkName($scope.lastName) &&
            $scope.checkMail($scope.email) &&
            $scope.checkPass($scope.pass) &&
            $scope.confirmPass($scope.passConf)) {

            $.post("api/", {
                'q':'createUser',
                'firstname':$scope.firstName,
                'lastname':$scope.lastName,
                'email':$scope.email,
                'password':$scope.pass
            }, function(data) {
                $window.location.href = "#!/login";
            }).fail(function(response) {
                $('#login-page .form .info').text(response.responseText);
            });
        }
    }
    $scope.checkName = function(name) {
        var re = /^[A-Za-z ]+$/;
        if (re.test(name) && name.length > 0) {
            $('#login-page .form .info').text("");
            return true;
        } else {
            $('#login-page .form .info').text("A name must only contain letters!");
            return false;
        }
    }
    $scope.checkMail = function(mail) {
        var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        if (re.test(mail) && mail.length > 0) {
            $('#login-page .form .info').text("");
            return true;
        } else {
            $('#login-page .form .info').text("The email you entered is invalid!");
            return false;
        }
    }
    $scope.checkPass = function(pass) {
        if (pass.length >= 8) {
            $('#login-page .form .info').text("");
            return true;
        } else {
            $('#login-page .form .info').text("Your password must contain at least 8 letters!");
            return false;
        }
    }
    $scope.confirmPass = function(pass) {
        if (pass == $scope.pass) {
            $('#login-page .form .info').text("");
            return true;
        } else {
            $('#login-page .form .info').text("Passwords do not match!");
            return false;
        }
    }
}]);

angular.module('app').controller('searchController', ['$scope', 'results', '$routeParams', function($scope, results, $routeParams) {
    $scope.tracks = results.tracks.items;
    $scope.artists = results.artists.items;
    $scope.albums = results.albums.items;
    $scope.query = $routeParams.query;
    $scope.tracksCount = results.tracks.total;
    $scope.artistsCount = results.artists.total;
    $scope.albumsCount = results.albums.total;

    for (var i = 0; i < $scope.tracks.length; i++) {
        $scope.tracks[i].rank = i+1;
    }

    setTimeout(function(){$('.artists-albums .artists > .artist .image > img').each(function(){
        $(this).height($(this).width())
    })},0);
}]);

angular.module('app').controller('top50Controller', ['$scope', 'list', function($scope, list) {
    $('.main-menu .menu-item#top-50').addClass('active');
    $scope.tracks = list.items;

    for (var i = 0; i < $scope.tracks.length; i++) {
        $scope.tracks[i].track.rank = i+1;
    }

    $scope.filter = function(p) {
        $scope.tracks = jQuery.grep(list.items, function(a) {
            for (var i = 0; i < a.track.artists.length; i++) {
                if (a.track.artists[i].name.toLowerCase().indexOf(p) != -1) {
                    return true;
                }
            }
            return a.track.name.toLowerCase().indexOf(p) != -1 || a.track.album.name.toLowerCase().indexOf(p) != -1;
        });
    }
}]);

angular.module('app').controller('homeController', ['$scope', function($scope) {
}]);
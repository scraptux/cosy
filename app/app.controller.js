angular.module('app').controller('appController', ['$scope', 'playerService', '$window', function($scope, playerService, $window) {
    $scope.player = playerService;

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
}]);

angular.module('app').controller('albumController', ['$scope', 'results', function($scope, results) {
    $scope.album = results;
    console.log(results);
    $scope.totalDuration = 0;
    for (var i = 0; i < $scope.album.tracks.items.length; i++) {
        $scope.totalDuration += $scope.album.tracks.items[i].duration_ms;
        $scope.album.tracks.items[i].rank = i+1;
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
    $('.main-menu .menu-item#top-50').toggleClass('active');
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
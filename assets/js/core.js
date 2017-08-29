var tag = document.createElement('script');
tag.src = "https://www.youtube.com/iframe_api";
var firstScriptTag = document.getElementsByTagName('script')[0];
firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

var player;
var time_update_interval;
var service;

function onYouTubeIframeAPIReady() {
    player = new YT.Player('player', {
        width: 250,
        height: 140,
        playerVars: {
            color: 'white',
        	controls: 0,
        	showinfo: 0,
        	modestbranding: 0,
        	disablekb: 1,
        	iv_load_policy: 3,
        	rel: 0,
        	wmode: 'opaque'
        },
        events: {
            onReady: onPlayerReady,
            onStateChange: onPlayerStateChange
        }
    });
}

function onPlayerStateChange(state) {
    switch(state.data) {
        case 1: // playing
            service.playing.playing = true;
            $('body').scope().$apply();
            $('.track-length').text(formatTime(player.getDuration()));
            time_update_interval = setInterval(function () {
                updateProgressBar();
            }, 10);
            $('.toggle-play').html('pause');
            $('.track.playing > .image > .indicator').html('pause');
            break;
        case 0: // stopped
            if (service.playing.repeatingOne) {
                player.playVideo();
            } else {
                service.next();
            }
        case 2: // paused
        case -1: // not started
        case 3: // buffering
        case 5: // positioned
            clearInterval(time_update_interval);
            $('.toggle-play').html('play_arrow');
            $('.track.playing > .image > .indicator').html('play_arrow');
            service.playing.playing = false;
            $('body').scope().$apply();
            break;
    }
}

function formatTime(time) {
    time = Math.round(time);
    var minutes = Math.floor(time/60);
    seconds = time - minutes*60;
    seconds = seconds < 10 ? '0'+seconds : seconds;
    if (isNaN(minutes) || isNaN(seconds)) {
        return "0:00";
    }
    return minutes + ":" + seconds;
}

function updateProgressBar() {
    var t = player.getCurrentTime();
    $('.progress-bar .elapsed').width((t/player.getDuration())*100+"%");
    $('.elapsed-time').text(formatTime(t));
}

function updateVolumeBar() {
    $('.volume-bar .elapsed').width(player.getVolume()+"%");
}

function onPlayerReady() {
    //service = $('body').injector().get('playerService');
    //if (service.playing.repeatingOne) {
        //$('.repeat.repeating-one').html('repeat_one');
    //}
    //player.setVolume(service.volume);
    //player.cueVideoById(service.playing.identifier);
    //$('.track-length').text(formatTime(player.getDuration()));
    //updateVolumeBar();
    service = $('body').injector().get('playerService');
    setTimeout(function(){updateVolumeBar();}, 100);
}

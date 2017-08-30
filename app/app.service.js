angular.module("app").service("playerService", [function () {
    this.playing = { "playing":false,
        "repeating":false,
        "repeatingOne":false,
        "shuffling":false,
        "new":true,
        "track":null
    };
    this.queue = [];
    this.originalQueue = [];

    this.queueAdd = function(track) {
        if (this.queue.indexOf(track) !== -1) {
            return;
        }
        this.queue.push(track);
        this.originalQueue.push(track);
        localStorage.queue = JSON.stringify(this.originalQueue);
    }

    this.queueDel = function(track) {
        var index = this.queue.indexOf(track);
        this.queue.splice(index,1);
        index = this.originalQueue.indexOf(track);
        this.originalQueue.splice(index,1);
        localStorage.queue = JSON.stringify(this.originalQueue);
    }

    this.clearQueue = function() {
        this.queue = [];
        this.originalQueue = [];
        localStorage.queue = JSON.stringify([]);
    }

    this.togglePlay = function() {
        if (this.playing.playing) {
            player.pauseVideo();
        } else {
            player.playVideo();
        }
    }

    this.toggleTrack = function(track) {
        $('.track.playing > .image > .indicator').html('play_arrow');
        if (this.playing.track == track) {
            this.togglePlay();
        } else {
            player.loadVideoById(track.video);
            this.playing.playing = false;
            this.playing.track = track;
            this.playing.new = false;
            if (this.queue.indexOf(track) == -1) {
                this.clearQueue();
                this.queueAdd(track);
            }
        }
        localStorage.playing = JSON.stringify(this.playing);
	}

    this.next = function() {
        if (this.playing.track.video != "") {
            var i = this.queue.indexOf(this.playing.track);
            if (i == -1) {
                this.toggleTrack(this.queue[0]);
            } else if (i >= this.queue.length-1 && this.playing.repeating) {
                this.toggleTrack(this.queue[0]);
            } else if (i >= this.queue.length-1 && !this.playing.repeating) {
                player.cueVideoById(this.playing.track.video);
            } else {
                this.toggleTrack(this.queue[i+1]);
            }
        }
    }

    this.prev = function() {
        if (this.playing.track.video != "") {
            var i = this.queue.indexOf(this.playing.track);
            if (i == -1) {
                this.toggleTrack(this.queue[this.queue.length-1]);
            } else if (i == 0 && this.playing.repeating) {
                this.toggleTrack(this.queue[this.queue.length-1]);
            } else if (i == 0 && !this.playing.repeating) {
                player.cueVideoById(this.playing.track.video);
            } else {
                this.toggleTrack(this.queue[i-1]);
            }
        }
    }

    this.toggleRepeat = function() {
        if (!this.playing.repeating && !this.playing.repeatingOne) {
            this.playing.repeating = true;
        } else if (this.playing.repeating) {
            $('.repeat.repeating').html('repeat_one');
            this.playing.repeating = false;
            this.playing.repeatingOne = true;
        } else {
            $('.repeat.repeating-one').html('repeat');
            this.playing.repeatingOne = false;
        }
        localStorage.playing = JSON.stringify(this.playing);
    }

    this.toggleShuffle = function() {
        this.playing.shuffling = !this.playing.shuffling;
        if (this.playing.shuffling) {
            this.originalQueue = this.queue.concat();
            for (var i = this.queue.length - 1; i > 0; i--) {
                var j = Math.floor(Math.random() * (i + 1));
                var temp = this.queue[i];
                this.queue[i] = this.queue[j];
                this.queue[j] = temp;
            }
        } else {
            this.queue = this.originalQueue.concat();
            this.originalQueue = [];
        }
        localStorage.playing = JSON.stringify(this.playing);
    }

    this.convertMs = function(ms) {
        var m = Math.floor(ms / 60000);
        var s = ((ms % 60000) / 1000).toFixed(0);
        return m + ":" + (s < 10 ? '0' : '') + s;
    };
}]);
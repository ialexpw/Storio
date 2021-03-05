/**
* A simple video player.
* @author Jayden Seric
* @param  {Object}      options                  - Options object.
* @param  {HTMLElement} options.element          - Container.
* @param  {string}      [options.playClass=play] - Container class name when video is playing.
* @param  {string}      [options.muteClass=mute] - Container class name when video is mute.
*/
function VideoPlayer(options) {
	var self = this;
	// Options
	self.element   = options.element;
	self.playClass = options.playClass || 'play';
	self.muteClass = options.muteClass || 'mute';
	// Derived
	self.video            = element.querySelector('video');
	self.playToggleButton = element.querySelector('.play-toggle');
	self.muteToggleButton = element.querySelector('.mute-toggle');
	// Handle play
	self.video.addEventListener('play', function() {
	  self.classList.add(self.playClass);
	});
	// Handle pause
	self.video.addEventListener('pause', function() {
	  self.classList.remove(self.playClass);
	});
	// Handle mute
	self.video.addEventListener('volumechange', function() {
	  if (self.video.muted) self.element.classList.add(self.muteClass);
	  else self.element.classList.remove(self.muteClass);
	});
	// Enable play toggle button
	self.playToggleButton.addEventListener('click', function() { self.togglePlay() });
	// Enable mute toggle button
	self.muteToggleButton.addEventListener('click', function() { self.toggleMute() });
  }
  
  /**
   * Toggles video play/pause.
   */
  VideoPlayer.prototype.togglePlay = function() {
	if (this.video.paused) this.video.play();
	else this.video.pause();
  };
  
  /**
   * Toggles video mute/unmute.
   */
  VideoPlayer.prototype.toggleMute = function() {
	this.video.muted = !this.video.muted;
  };
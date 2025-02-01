useWorker: true

<div id="id01" class="w3-modal" style="z-index: 9999;">
  <div class="w3-modal-content w3-card-4 w3-animate-zoom" style="max-width:600px; position: relative;">
    <div class="w3-center w3-margin"><br>
      <span onclick="closeModal()" class="w3-button w3-xlarge w3-transparent w3-display-topright" title="Close Modal">&times;</span><br>
      <img src="sources/logo.jpg" style="text-align:center; width:400px"><br>
      <h2>Beta Testing Notification</h2>
      <p>This website is currently in beta testing. Some features may not be fully functional or available at this time. We appreciate your understanding and welcome your feedback to improve the platform.</p>
      <a onclick="closeModal()" class="w3-button w3-round w3-hover-shadow w3-round-xlarge w3-center" style="margin-bottom:10px;width:80%;background: #FFBF00">Okay, Let's Go</a><br>
      <br>
    </div>
    </div>
  </div>

  <canvas id="confetti-canvas" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none; z-index: 1;"></canvas>
  <audio id="confetti-sound" src="path/to/your/soundfile.mp3" loop></audio>

  <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.4.0/dist/confetti.browser.min.js"></script>
  <script>
    var confettiInterval;
    var confettiSound = document.getElementById('confetti-sound');

    window.addEventListener("load", function(){
      setTimeout(function open(event){
        document.getElementById('id01').style.display='block';
        var confettiCanvas = document.getElementById('confetti-canvas');
        var myConfetti = confetti.create(confettiCanvas, {
          resize: true,
        });
        myConfetti({
          particleCount: 100,
          spread: 160,
          origin: { y: 0.6 }
        });
        confettiSound.play();
        confettiInterval = setInterval(launchConfetti, 5000);
      }, 500);
    });

    function launchConfetti() {
      var confettiCanvas = document.getElementById('confetti-canvas');
      var myConfetti = confetti.create(confettiCanvas, {
        resize: true,
      });
      myConfetti({
        particleCount: 100,
        spread: 160,
        origin: { y: 0.6 }
      });
    }

    function closeModal() {
      document.getElementById('id01').style.display='none';
      clearInterval(confettiInterval);
      confettiSound.pause();
      confettiSound.currentTime = 0;
    }
  </script>

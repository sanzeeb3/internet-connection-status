/**
 * @see  https://gist.github.com/mhuggins/28c387ebb665c4b73db1d3af61d6dcec
 *
 */
var KEY_LEFT = 37;
var KEY_UP = 38;
var KEY_RIGHT = 39;
var KEY_DOWN = 40;

var vendors = ["webkit", "moz", "o", "ms"];

for (var x = 0; x < vendors.length && !window.requestAnimationFrame; x++) {
    window.requestAnimationFrame = window[vendors[x] + "RequestAnimationFrame"];
    window.cancelAnimationFrame = window[vendors[x] + "CancelAnimationFrame"] ||
                                  window[vendors[x] + "CancelRequestAnimationFrame"];
}

function Game(context) {
  this.context = context;
  this.boardSize = 20;
  this.tileSize = 20;
  this.fps = 20;
  this.segments = [];
  this.length = 5;
  this.position = { x: this.boardSize / 2, y: this.boardSize / 2 };
  this.foodPosition = this._getRandomPosition();
  this.direction = { x: 1, y: 0 };
}

Game.prototype.start = function(win) {
  document.addEventListener("keydown", function(event) {
    switch (event.keyCode) {
      case KEY_LEFT:
        if (this.direction.x === 0) {
          this.direction = { x: -1, y: 0 };
        }
        break;
      case KEY_UP:
        if (this.direction.y === 0) {
          this.direction = { x: 0, y: -1 };
        }
        break;
      case KEY_RIGHT:
        if (this.direction.x === 0) {
          this.direction = { x: 1, y: 0 };
        }
        break;
      case KEY_DOWN:
        if (this.direction.y === 0) {
          this.direction = { x: 0, y: 1 };
        }
        break;
    }
  }.bind(this));

  this.lastTime = Date.now();
  this._loop(win);
};

Game.prototype._loop = function(win) {
  var fpsInterval = 1000 / this.fps;
  var currentTime = Date.now();
  var elapsedTime = currentTime - this.lastTime;

  win.requestAnimationFrame(function() {
    if (elapsedTime > fpsInterval) {
      this._move();
      this._draw();
      this.lastTime = currentTime - (elapsedTime % fpsInterval);
    }

    this._loop(win);
  }.bind(this));
};

Game.prototype._move = function() {
  this.position = { x: this.position.x + this.direction.x, y: this.position.y + this.direction.y };

  ["x", "y"].forEach(function(direction) {
    while (this.position[direction] < 0) {
      this.position[direction] += this.boardSize;
    }
    while (this.position[direction] >= this.boardSize) {
      this.position[direction] -= this.boardSize;
    }
  }.bind(this));

  this.segments.push(this.position);

  while (this.segments.length > this.length) {
    this.segments.shift();
  }

  if (this.position.x === this.foodPosition.x && this.position.y === this.foodPosition.y) {
    this.length++;
    this.foodPosition = this._getRandomPosition();
  }
};

Game.prototype._draw = function() {
  this.context.clearRect(0, 0, this.boardSize * this.tileSize, this.boardSize * this.tileSize);

  this.context.fillStyle = "rgb(0, 0, 0)";
  this.context.fillRect(0, 0, this.boardSize * this.tileSize, this.boardSize * this.tileSize);

  this.context.fillStyle = "rgb(192, 192, 192)";
  this.segments.forEach(function(segment) {
    this.context.fillRect(segment.x * this.tileSize, segment.y * this.tileSize, this.tileSize, this.tileSize);
  }.bind(this));

  this.context.fillStyle = "rgb(0, 255, 0)";
  this.context.fillRect(this.foodPosition.x * this.tileSize, this.foodPosition.y * this.tileSize, this.tileSize, this.tileSize);
};

Game.prototype._getRandomPosition = function() {
  return {
    x: Math.floor(Math.random() * this.boardSize),
    y: Math.floor(Math.random() * this.boardSize)
  };
};

var canvas = document.getElementById("game");
var context = canvas.getContext("2d");

if (context !== null) {
  new Game(context).start(window);
} else {
  alert("Unable to obtain 2D canvas context.");
}
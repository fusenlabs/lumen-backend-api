var api_path = 'http://fusenlabs.co.s192176.gridserver.com/imt';
//var api_path = '';
var Client = function Client() {
    this.authClient = null;

    this._setupAuth();
    this._setupEventHandlers();
}

Client.prototype._login = function _login() {
    var self = this;

    $.ajax({
        url: api_path + "/login",
        method: "POST",
        data: {
            credentials: {
                username: 'user@user.com',
                password: '1234'
            }
        },
        statusCode: {
            200: function(response) {
                if (response.accessToken === undefined) {
                    alert('Something went wrong');
                } else {
                    self.authClient.login(response.accessToken, response.accessTokenExpiration);
                }
            },
            401: function() {
                alert('Login failed');
            }
        }
    });
}

Client.prototype._login_fb_client = function _login() {
    var self = this;
    var data = window.flashFBCredentials;
    //window.flashFBCredentials = {};
    $.ajax({
        url: api_path + "/login/facebook",
        method: "POST",
        data: data,
        crossDomain: true,
        headers: {"Access-Control-Allow-Origin": "http://api.localhost"},
        //contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
        statusCode: {
            200: function(response) {
                if (response.accessToken === undefined) {
                    alert('Something went wrong');
                } else {
                    self.authClient.login(response.accessToken, response.accessTokenExpiration);
                }
            },
            401: function() {
                alert('Client authorization failed');
            }
        }
    });
}

Client.prototype._logout = function _logout() {
    this.authClient.logout();
}

Client.prototype._request = function _request() {
    var resource = $.ajax({
        url: api_path + "/api/resource",
        /*crossDomain: true,*/
        headers: {"Access-Control-Allow-Origin": "http://api.localhost"},
        statusCode: {
            400: function() {
                alert('Since we did not send an access token we get client error');
            },
            401: function() {
                alert('You are not authenticated, if a refresh token is present will attempt to refresh access token');
            }
        }
    })
    .done(function(data) {
        alert(JSON.stringify(data));
    });
}

Client.prototype._setupAuth = function _setupAuth() {
    var self = this;

    this.authClient = new jqOAuth({
        events: {
            login: function() {
                alert("You are now authenticated.");
            },
            logout: function() {
                alert("You are now logged out.");
            },
            tokenExpiration: function() {
                return $.post(api_path + "/refresh-token").success(function(response){
                    self.authClient.setAccessToken(response.accessToken, response.accessTokenExpiration);
                });
            }
        }
    });
}

Client.prototype._saveScore = function _saveScore() {
    var userId = $("#userId").val();
    var genreId = $("#genreId").val();
    var score = $("#score").val();
    /*console.log(userId);
    console.log(genreId);
    console.log(score);*/
    var resource = $.ajax({
        url: api_path + "/api/score/" + genreId,
        method: "POST",
        data: {points: score, friends: '13'},
        crossDomain: true,
        headers: {"Access-Control-Allow-Origin": "http://api.localhost"},
        statusCode: {
            400: function() {
                alert('Since we did not send an access token we get client error');
            },
            401: function() {
                alert('You are not authenticated, if a refresh token is present will attempt to refresh access token');
            }
        }
    })
    .done(function(data) {
        alert(JSON.stringify(data));
    });
}

Client.prototype._getLeaderboard = function _getLeaderboard() {
    var genreId = $("#genreId").val();
    var resource = $.ajax({
        url: api_path + "/api/score/" + genreId,
        crossDomain: true,
        headers: {"Access-Control-Allow-Origin": "http://api.localhost"},
        statusCode: {
            400: function() {
                alert('Since we did not send an access token we get client error');
            },
            401: function() {
                alert('You are not authenticated, if a refresh token is present will attempt to refresh access token');
            }
        }
    })
    .done(function(data) {
        alert(JSON.stringify(data));
    });
}

Client.prototype._setFriends = function _setFriends() {
    var friends_ids = $('#friends_ids').val();
    var resource = $.ajax({
        url: api_path + "/api/friends",
        data: { ids: friends_ids },
        method: "POST",
        crossDomain: true,
        headers: {"Access-Control-Allow-Origin": "http://api.localhost"},
        statusCode: {
            400: function() {
                alert('Since we did not send an access token we get client error');
            },
            401: function() {
                alert('You are not authenticated, if a refresh token is present will attempt to refresh access token');
            }
        }
    })
    .done(function(data) {
        alert(JSON.stringify(data));
    });
}

Client.prototype._setupEventHandlers = function _setupEventHandlers() {
    $("#login").click(this._login.bind(this));
    $("#request").click(this._request.bind(this));
    $("#logout").click(this._logout.bind(this));
    $("#login_fb_client").click(this._login_fb_client.bind(this));
    $("#save_score").click(this._saveScore.bind(this));
    $("#leaderboard").click(this._getLeaderboard.bind(this));
    $("#friends").click(this._setFriends.bind(this));
}

$(document).ready(function() {
    window.client = new Client;
});
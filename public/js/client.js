var Client = function Client() {
    this.authClient = null;

    this._setupAuth();
    this._setupEventHandlers();
}

Client.prototype._login = function _login() {
    var self = this;

    $.ajax({
        url: "/login",
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

Client.prototype._login_client_token = function _login() {
    var self = this;
    var data = {
        client_id: 1,
        client_secret: 'gKYG75sw',
        grant_type: 'client_credentials'
    };
    $.ajax({
        url: "/oauth/access-token",
        method: "POST",
        data: data,
        //contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
        statusCode: {
            200: function(response) {
                if (response.access_token === undefined) {
                    alert('Something went wrong');
                } else {
                    self.authClient.login(response.access_token, response.expires_in);
                }
            },
            401: function() {
                alert('Login failed');
            }
        }
    });
}

Client.prototype._logout = function _logout() {
    this.authClient.logout();
}

Client.prototype._request = function _request() {
    var resource = $.ajax({
        url: "/api/resource", 
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
                return $.post("/refresh-token").success(function(response){
                    self.authClient.setAccessToken(response.accessToken, response.accessTokenExpiration);
                });
            }
        }
    });
}

Client.prototype._setupEventHandlers = function _setupEventHandlers() {
    $("#login").click(this._login.bind(this));
    $("#request").click(this._request.bind(this));
    $("#logout").click(this._logout.bind(this));
    $("#login_client_token").click(this._login_client_token.bind(this));
}

$(document).ready(function() {
    window.client = new Client;
});
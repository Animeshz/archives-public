const API_KEY = "AIzaSyD9aedfJUSROqYb2Ku8JEHgfyKDA3ZKo7U";

// Future scopes: multiple account (+dropdown)
function retrieve_token() {
    chrome.identity.getAuthToken({ interactive: true }, auth_token => chrome.storage.local.set({channel_token: auth_token}));
    chrome.identity.launchWebAuthFlow({'url': 'https://accounts.google.com/o/oauth2/v2/auth?scope=https%3A//www.googleapis.com/auth/youtube&include_granted_scopes=true&response_type=token&client_id=170889941730-kh93arhfkdrfdi2aepr9gp7vtmufriqd.apps.googleusercontent.com&redirect_uri=' + chrome.identity.getRedirectURL(), 'interactive': true}, function(redirect_url) { console.log(redirect_url); });
    // A working url
    // https://accounts.google.com/o/oauth2/auth/oauthchooseaccount?client_id=897699766288-cgmjrlkmtjllt3736fjja62j6ocr08op.apps.googleusercontent.com&redirect_uri=https%3A%2F%2Fbuilder.zety.com%2Fsignin%2Faccounts%2Fv4%2Fexternalloginresponse&response_type=code&scope=email&state=R0dMRXx8UldafGZhbHNlfHRydWV8aHR0cHM6Ly9idWlsZGVyLnpldHkuY29tL3Jlc3VtZS9maW5hbC1yZXN1bWV8dW5kZWZpbmVkfFJlc3VtZXN8fFt7ImRvY0lkIjoiY2M3OGVlMTktNTY4NC00MWM1LWIzMzUtNDUxNzQ2N2VmYTAwIn1dfHwxfGh0dHBzOi8vemV0eS5jb20v&flowName=GeneralOAuthFlow
}
document.getElementById("authorize").addEventListener('onClick', retrieve_token);

chrome.storage.local.get({ channel_token: undefined, channel_playlist: undefined }, (ret) => {
    if (!ret.channel_token) display_retrieve_token();
    else if (!ret.playlist) display_configure_playlist();
});

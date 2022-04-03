function message_listener(message, sender, sendResponse) {
    if (message.type == 'queue_download') {
        // Download and save into indexeddb
        message.options.video_id
        message.options.url
        if (message.options.audio_url != null) message.options.audio_url
    }
}

function download_start() {
    //initialize_queue();

    chrome.storage.local.get({ downloads_info: {} }, ret => {
        Object.keys(downloads_info).filter(k => !downloads_info[k].downloaded)
            .map(video_id => {
                fetch("https://www.youtube.com/youtubei/v1/player?key=AIzaSyAO_FJ2SlqU8Q4STEHLGCilw_Y9_11qcW8", {
                        method: 'POST',
                        body: JSON.stringify({
                            videoId: video_id,
                            context: { "client": { "clientName": "WEB", "clientVersion": "2.20201021.03.00" } }
                        })
                    })
                    .then(response => response.json())
                    .then(data => data.streamingData.formats.concat(data.streamingData.adaptiveFormats))
                    .then(info => [
                        info.find(e => e.itag == downloads_info[video_id].itag),
                        ...downloads_info[video_id].audio_itag ? [e.itag == downloads_info[video_id].audio_itag] : []
                    ])
                    .then(vid_aud_info => {
                        // Download and save into indexeddb
                        video_id
                        vid_aud_info[0].url
                        if (vid_aud_info.length() > 1) vid_aud_info[1].url
                    });
            })
    });

    chrome.runtime.onMessage.addListener(message_listener);
}

function download_start() {
    //destroy_queue();
    chrome.runtime.onMessage.removeListener(message_listener);
}

chrome.storage.local.get({ download: false }, (ret) => {
    if (ret.download) {
        download_start();
    }
});

chrome.storage.onChanged.addListener(function(changes, namespace) {
    for (let [key, { oldValue, newValue }] of Object.entries(changes)) {
        if (namespace == "local" && key == "download" && oldValue != newValue) {
            if (newValue) {
                download_start();
            } else {
                download_stop();
            }
        }
        console.log(
            `Storage key "${key}" in namespace "${namespace}" changed.`,
            `Old value was "${oldValue}", new value is "${newValue}".`
        );
    }
});
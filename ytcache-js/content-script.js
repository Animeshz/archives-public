// Send signal to background script to queue download
// Do all checks in js, also initialize queue from popup.js (maybe?), export functions from wasm only (maybe?).

function clear_video(vid) {
    chrome.storage.local.get({ downloads_info: {} }, ret => {
        delete ret.downloadeds_info[vid];
        chrome.storage.local.set({ downloads_info: ret.downloads_info });

    });
    // delete video from indexeddb
}

new MutationObserver(() => {
    if (location.href.includes("/watch")) {
        if (document.getElementById("ytcache-save-btn") != null) return;

        let info_contents = document.getElementById("info-contents");
        if (info_contents == null) return;
        let content_menu = info_contents.querySelector("#top-level-buttons-computed");
        if (content_menu == null) return;

        let save_btn = document.createElement("div");
        save_btn.id = "ytcache-save-btn";
        save_btn.className = "btn-group";
        save_btn.innerHTML =
            '<button id="save-btn" type="button" class="btn btn-danger">Save</button>' +
            '<button type="button" class="btn btn-danger dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false"></button>' +
            '<ul id="ytcache-split-holder" class="dropdown-menu"></ul>';
        content_menu.prepend(save_btn);

        let video_id = (new URL(location.href)).searchParams.get("v");
        chrome.storage.local.get({ downloads_info: {} }, ret => {
            let info = ret.downloads_info[video_id];
            if (info != undefined && info.downloaded) {
                let target = save_btn.querySelector("#save-btn");
                target.innerText = info.qualityLabel + " (" + (info.contentLength / 1024 / 1024).toFixed(1) + "M) - Remove";
                target.onclick = e => {
                    clear_video(video_id);
                };

                let our_source = new MediaSource();
                document.getElementsByTagName("video")[0].src = URL.createObjectURL(mediaSource);
                our_source.addEventListener('sourceopen', (_) => {
                    let video_buffer = this.addSourceBuffer(info.mime);
                    // streaming fetch video_buffer from indexeddb and append
                    // https://dumbmatter.com/2021/06/streaming-data-from-indexeddb/
                    // fetch(info.video_path, buf => video_buffer.appendBuffer(buf)); but stream
                    if (info.audio_mime != undefined) {
                        let audio_buffer = this.addSourceBuffer(info.audio_mime);
                        //same for audio
                    }
                });
            }
        });

        let split_holder = save_btn.querySelector("#ytcache-split-holder");
        fetch("https://www.youtube.com/youtubei/v1/player?key=AIzaSyAO_FJ2SlqU8Q4STEHLGCilw_Y9_11qcW8", {
                method: 'POST',
                body: JSON.stringify({
                    videoId: video_id,
                    context: { "client": { "clientName": "WEB", "clientVersion": "2.20201021.03.00" } }
                })
            })
            .then(response => response.json())
            .then(data => {
                data.streamingData.formats.forEach(v => v.fast = true);
                return data.streamingData.formats.concat(data.streamingData.adaptiveFormats);
            })
            .then(information => {
                let audio = information.filter(e => e.mimeType.startsWith("audio")).sort((a, b) => parseInt(b.qualityLabel) - parseInt(a.qualityLabel))[0];
                information
                    .filter(e => e.qualityLabel != undefined)
                    .sort((a, b) => {
                        //sort_by(quality -> then size)
                        let ret = parseInt(b.qualityLabel) - parseInt(a.qualityLabel);
                        if (ret == 0) ret = b.contentLength - a.contentLength;
                        return ret;
                    })
                    .forEach(element => {
                        let option = document.createElement("li");
                        option.className = "dropdown-item";
                        option.innerText = element.qualityLabel + " (" + (element.contentLength / 1024 / 1024).toFixed(1) + "M)" + (element.fast ? " - Fast" : "");
                        option.onclick = e => {
                            console.log("you picked " + element.qualityLabel);
                            chrome.storage.local.get({ downloads_info: {} }, ret => {
                                ret.downloads_info[video_id] = {
                                    downloaded: false,
                                    itag: element.itag,
                                    mime: element.memeType,
                                    audio_itag: audio.itag,
                                    audioMime: element.memeType,
                                    qualityLabel: element.qualityLabel,
                                    contentLength: element.contentLength,
                                    mime
                                };
                                chrome.storage.local.set({ downloads_info: ret.downloads_info });

                                chrome.runtime.sendMessage({
                                    type: "queue_download",
                                    options: {
                                        video_id: video_id,
                                        url: url,
                                        audio_url: element.fast ? undefined : audio.url
                                    }
                                });
                            });
                        };
                        split_holder.append(option);
                    });
            });
    } else if (location.href.includes("/playlist")) {
        chrome.storage.local.get({ downloads_info: {} }, ret => {
            let contents = document.getElementById("contents");
            let videos = contents.getElementsByTagName("ytd-playlist-video-renderer");
            for (let video of videos) {
                let video_title = video.querySelector("a#video-title");
                let video_id = (new URL(video_title.href)).searchParams.get("v");
                if (ret.downloads_info[video_id] != undefined) {
                    video_title.innerText += " - Saved " + element.qualityLabel + " (" + (element.contentLength / 1024 / 1024).toFixed(1) + "M)";
                }
            }
        });
    }
}).observe(document, { subtree: true, childList: true });
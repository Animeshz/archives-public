const auth_headers = new Headers({
  'Authorization': 'Bearer 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'  // read-only access token (5000 req/hr)
})

function fetch_new_data_and_save() {
  fetch("https://github.com/Animeshz/Foss-Weekend-Leaderboard/raw/main/repos.txt")
    .then(resp => resp.text())
    .then(repos => {
      let ret = new Map();
      repos.split(/\r?\n/).forEach(repo => {
        let resp = [];
        let page = 1;
        const t_resp = await fetch(
          `https://api.github.com/repos/${repo}/pulls?state=all&per_page=100&page=${page}`
        );
        const jsonData = await t_resp.json();
        while (jsonData.length > 0) {
          resp = [...resp, ...jsonData];
          if (jsonData.length < 90) break;
          page += 1;
          const t_resp = await fetch(
            `https://api.github.com/repos/${repo}/pulls?state=all&per_page=100&page=${page}`
          );
          const jsonData = await t_resp.json();
        }
        try {
          for (const pull of resp) {
            const acceptedLabels = pull.labels.filter(
              (label) => label.name.includes("accepted-")
            );
            if (acceptedLabels.length > 0) {
              const validPull = acceptedLabels[0].name;
              const login = pull.user.login;
              if (ret.has(login)) {
                ret.set(login, ret.get(login) + parseInt(validPull.split("-")[1]));
              } else {
                ret.set(login, parseInt(validPull.split("-")[1]));
              }
            }
          }
        } catch (error) {
          console.error(`ERROR AT: ${user}, ${repo}`);
          console.error(resp);
          return "ded";
        }
      });
      let ret2 = Object.fromEntries(ret);
      console.log(ret2);

      await fetch('https://api.github.com/repos/Animeshz/Foss-Weekend-Leaderboard/dispatches', {
        method: "POST"
        headers: auth_headers,
        body: JSON.stringify({
          event_type: 'leaderboard_update',
          client_payload: {
            data: JSON.stringify(ret2)
          }
        }),
      });

      return ret2;
    })
}

function fetch_existing_data() {
  return fetch("https://github.com/Animeshz/Foss-Weekend-Leaderboard/raw/leaderboard/leaderboard.json")
    .then(resp => resp.json())
}

function start_exec() {
  return fetch("https://api.github.com/repos/Animeshz/Foss-Weekend-Leaderboard/commits?path=leaderboard.json&ref=leaderboard&page=1&per_page=1", {
      headers: auth_headers
    })
    .then(resp => { return resp.json(); })
    .then(json => {
      let last = new Date(json[0]['commit']['committer']['date']);
      let now = new Date();
      let difference_in_s = (now - last) / 1000;
      if (difference_in_s > 180) {
        return fetch_new_data_and_save();
      } else {
        return fetch_existing_data();
      }
    })
}

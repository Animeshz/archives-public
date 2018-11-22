# ClusterPlus 
<img src="https://i.imgur.com/nBa6wSK.png" alt="ClusterPlus logo" width="128" height="128">
A multipurpose discord bot.

  - Android GUI support with amazing and special UI and UX
  - User friendly experience
  - An Artificial Intelligence (beta) talking system

#### Features!

  - Custom Command Creation (Dynamic)
  - Custom Module Attachment+Creator (Dynamic)
  - Create forms and recieve data
  - Create polls and recieve data
  - Have suggestion and report commands for direct contact and very fast reply
  - Stylize your text with unicode letters
  - Create invite to get bot in your server

### Install

ClusterPlus requires [PHP v7+ with ZTS extention](https://stackoverflow.com/questions/44756284/how-to-compile-php-7-1-with-zts).

Create a config.json file in root of project.
Example:
```json
{
	"clientConfig": {
		"commandPrefix": "$",
		"owners": ["your user id"],
		"unknownCommandResponse": false,
		"invite": "https://discord.gg/UHTpYc9",
		"database": {
			"server": "127.0.0.1",
			"user": "root",
			"pass": "",
			"db": "discord-bot"
		},
		"pool.options": {
			"size": 7
		},
		"guild.info": {
			"guild.id": 1234567890,
			"channel.report.id": 1234567890,
			"channel.suggestion.id": 1234567890
		},
		"dialogflow": "/path/to/your/dialogflow/config",
		"game": "Set playing status for your bot"
	},
	"token": "Bot's token"
}
```

Install the dependencies and devDependencies and start the server.
```sh
$ cd clusterplus
$ composer install
```

For production environments...

```sh
$ cd clusterplus
$ composer install --no-dev
```

### Development

Want to contribute? Great!

ClusterPlus uses [ReactPHP](https://reactphp.org/) at its core and uses thread based environment along with pThreads with zts-extention enabled.

See https://github.com/elazar/asynchronous-php for async programming resources.
See [how to compile php with zts](https://stackoverflow.com/questions/44756284/how-to-compile-php-7-1-with-zts) for more information about compilation of php along with zts-extention.

Open your favorite Terminal and run command to run the .

```sh
$ php clusterplus.php
```

### Todos

 - Write Tests
 - Create Continuous integration for this project
 - Make some options optional in configs
 - Bug fixes

License
----

GNU General Public License v3 or later
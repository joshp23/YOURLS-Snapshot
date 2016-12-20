# YOURLS-Snapshot
YOURLS URL preview plugin with image caching powered by PhantomJS

by Josh Panter [Unfettered](https://unfettered.net)

Snapshot is a visual preview plugin for [YOURLS](https://yourls.org/) personal URL shortener that uses the power of [PhantomJS](http://phantomjs.org/) headless web browser via the [Screen](https://github.com/microweber/screen) PHP library.

## Features & Function

#### Easy Configuration
1. Just append your short URL with a '~', or choose a custom character from the admin interface to trigger a preview.
2. Clear instructions: Includes detailed, personalized instruction for setting up a subdomain service, and more.
3. Highly configurable PhantomJS options dialogue, makes working with the application trivial.
4. All options are configured in admin interface and stored in the databse, no file editing.
5. Screen is included and ready to go, no setup required.

#### Robust cache features: 
1. Set cache expiration times for rendered preview images.
2. Easily monitor cache disk usage via the admin interface.
3. Manual cache flush with granular configuration options.
4. Custom API for cache flushing.
5. Personalized cron example included for use with API, keep the cahce in check with a configurable auto-flush.
6. Self-cleaning: Cached images are deleted when a keyword is removed from database.

## Requirements and Installation
* Install and configure YOURLS
* Install phantomjs (details [here](http://phantomjs.org/download.html))
  * You can download the binary from the above link, or install via package manager
  ```
  $sudo apt-get install phantomjs
  ```
  * Install font requirements for phantomjs
  ```
  $sudo apt-get install fontconfig freetype*
  ```
* Grab the [latest release](https://github.com/joshp23/YOURLS-Snapshot/releases/latest) and extract the `snapshot` folder into `YOURLS/user/plugins/`
* Copy or link the file `YOURLS/user/plugins/snapshot/assets/srv.php` into `YOURLS/pages/`
* Create the directory `YOURLS/user/cache/` and make it writable by your webserver
* Recursively make the folder `YOURLS/user/plugins/snapshot/screen/jobs` writable by your webserver
* Go to the YOURLS `Manage Plugins` page and enable this plugin.
* Go to the `Snapshot` page and follow the instructions there.

### Optional
* configure a cron job to keep up on cache maintanence

#### Notes 
1. If you installed via binary, or some other method, make sure to take note of the location of the phantomjs binary.
2. Please see the [TODO](https://github.com/joshp23/YOURLS-Snapshot/issues/1) list for future feature enhancements

## Credits
OZH's original [preview](https://github.com/YOURLS/YOURLS/wiki/Plugin-%3D-Preview-URL) plugin was used as the original code base.

If you appreciate this code and want to show thanks, please feel free go to my website and drop a [donation](https://unfettered.net/donate).

### Disclaimer

This plugin is offered "as is", and may or may not work for you. Give it a try, and have fun! If you run into any problems, please open up an [issue](https://github.com/joshp23/YOURLS-Snapshot/issues) on github, or simply submit a pull request with your fix.

===========================

    Copyright (C) 2016 Josh Panter

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.

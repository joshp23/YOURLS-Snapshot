# YOURLS-Snapshot
YOURLS URL preview plugin with image caching powered by PhantomJS

Snapshot is a visual preview plugin for [YOURLS](https://yourls.org/) personal URL shortener that uses the power of [PhantomJS](http://phantomjs.org/) headless web browser via the [Screen](https://github.com/microweber/screen) PHP library.

## Features & Function

#### Easy Configuration
- Just append your short URL with a '~', or choose a custom character from the admin interface to trigger a preview.
- Clear, detailed, personalized instructions included for setting up a subdomain service, and more.
- Highly configurable PhantomJS options dialogue, makes working with the application trivial.
- All options are easily configured in admin interface and stored in the databse, no file editing.
- Screen is included and ready to go, no setup required.
- Cache directory created and moved automatically, no manual file system manipulation required.

#### Simple integration into other plugins
Because of the U-SRV method of image serving, it is a snap to integrate Snapshot into other plugins. Currently the [Rscrub](https://github.com/joshp23/YOURLS-rscrub) and [Compliance](https://github.com/joshp23/YOURLS-Compliance) plugins utilize this feature.

#### Seamless, out-of-the-box integration with other plugins
- If [Phishtank-2.0](https://github.com/joshp23/YOURLS-Phishtank-2.0) is installed, the previewed url will be (re)checked against the [Phishtank API](https://www.phishtank.com/)
    - If there is a hit, a very visible warning is displayed 
    - Otherwise the Phistank logo and a friendly message is displayed below the image
- If [Compliance](https://github.com/joshp23/YOURLS-Compliance) is installed, a small link and message under the preview image is displayed for potentially bad links.

#### Robust cache features: 
- Set cache expiration times for rendered preview images.
- Easily monitor cache disk usage via the admin interface.
- Manual cache flush with granular configuration options.
- Custom API for cache flushing.
- Personalized cron example included for use with API, keep the cahce in check with a configurable auto-flush.
- Self-cleaning: Cached images are deleted when a keyword is removed from database.

## Requirements and Installation
* Install and configure YOURLS
* Install phantomjs: details and prebuilt binaries can be found [here](http://phantomjs.org/download.html)
  * __NOTE:__ On Debian/Ubuntu there is a [known bug](https://bugs.debian.org/cgi-bin/bugreport.cgi?bug=808242) with upstream packaging. Installing the prebuilt binary is therefore reccomended. Ex:
  ```
  # cd /opt
  # wget https://bitbucket.org/ariya/phantomjs/downloads/phantomjs-2.1.1-linux-x86_64.tar.bz2
  # tar xjf phantomjs-2.1.1-linux-x86_64.tar.bz2
  # rm phantomjs-2.1.1-linux-x86_64.tar.bz2
  # ln -s /opt/phantomjs-2.1.1-linux-x86_64/bin/phantomjs /usr/bin/phantomjs
  # phantomjs --version
  2.1.1
  ```
  * Install font requirements for phantomjs
  ```
  $ sudo apt-get install fontconfig freetype*
  ```
* Grab Snapshot's [latest release](https://github.com/joshp23/YOURLS-Snapshot/releases/latest) and extract the `snapshot` folder into `YOURLS/user/plugins/`
* Copy or link the file `YOURLS/user/plugins/snapshot/assets/srv.php` into `YOURLS/pages/`
* Permissions:
  * Recursively make the folder `YOURLS/user/plugins/snapshot/screen/jobs` writable by your webserver
  * Make the directory `YOURLS/user` writable by your webserver OR create a `YOURLS/user/cache/snapshot/` directory that is writable by your webserver.
* Go to the YOURLS `Manage Plugins` page and enable this plugin.
* Go to the `Snapshot` page and follow the instructions there.

### Optional
* configure a cron job to keep up on cache maintanence

#### Notes 
1. If you installed via binary make sure totake note of the location of the phantomjs binary.
2. Your webserver needs to have write permissions in order to make the cache directory. If you run into errors, try making the director and setting `chmod 0777` manually. 
3. Please see the [TODO](https://github.com/joshp23/YOURLS-Snapshot/issues/1) list for future feature enhancements
4. By default Snapshot will keep an error log in its own directory provided permissions are correctly set. This behavior can be disabled in the admin section.

## Credits
OZH's original [preview](https://github.com/YOURLS/YOURLS/wiki/Plugin-%3D-Preview-URL) plugin was used as the original code base.

If you appreciate this code and want to show thanks, please feel free go to my website and drop a [donation](https://unfettered.net/donate).

### Disclaimer

This plugin is offered "as is", and may or may not work for you. Give it a try, and have fun! If you run into any problems, please open up an [issue](https://github.com/joshp23/YOURLS-Snapshot/issues) on github, or simply submit a pull request with your fix.

===========================

    Copyright (C) 2016 - 2017 Josh Panter

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

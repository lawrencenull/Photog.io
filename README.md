Photog.io
=========

__Photog.io__ is an open-source, self-hosted image sharing platform. Users on your Photog.io site can follow users on any other Photog.io site via the [PubSubHubbub](https://code.google.com/p/pubsubhubbub/) protocol.

Photog.io is made possible thanks to the following technologies:

* [CodeIgniter PHP Framework](http://CodeIgniter.com/)
* [Twitter Bootstrap](http://twitter.github.com/bootstrap/)
* [jQuery](http://jquery.com/)
* [Paul Irish's Infinite Scroll](https://github.com/paulirish/infinite-scroll)
* Various HTML5 enhancements such as [getUserMedia()](http://dev.w3.org/2011/webrtc/editor/getusermedia.html)

Photog.io is very much a work-in-progress, so many features are broken or nonexistent at this time. If you find a bug please file a report or submit a patch. Pull requests are greatly appreciated at this early stage of development! :)

If you have any questions or comments, don't hesitate to [get in touch!](http://twitter.com/HTMLbyJoe)

Goals
=====
* Simple to install
* Easy to upload/import photos
* A snap to download/export photos
* Effortless to view photos through the responsive web app

Inspiration
===========
* Ease of installation and open source (self hosted) like WordPress
	* (users are always in complete control of their data)
* Instagram’s design sense
	* (very easy and fun to take pictures with)
* Reddit’s homepage is visually similar whether you’re logged-in or not
* Tumblr’s minimalistic dashboard and infinite scrolling
* Imgur’s ease of uploading
	* (drag and drop)
* Personality and playfulness of Flickr
	* (saying “hello” in different languages)

Where Photog.io differs
=======================
* Users on one server can follow users on a different server (like in the days of RSS)
* Highly focused on web-app rather than native iOS/Android app
* Strives to be even more minimalistic than tumblr or instagram
	* Leave out everything that’s not completely essential
	* Make the most of every available space without making it feel cramped
	* Don't give users options they don't want, care about, or need

Requirements
============
* PHP 5.1.6 (or newer)
* MySQL 5.1 (or newer)

Installation
============
One of the main goals of Photog.io is to be as painless to install as possible. An installation wizard is planned for the future, but for now the manual installation process is explained in the following steps:

1. [Download Photog.io](https://github.com/JoeAnzalone/Photog.io/archive/master.zip), unzip it, and upload it to your server
2. Make sure you have a MySQL server set up and gather your host name, username, and password
3. Import "photog.io.sql" into your database
4. Navigate to /application/config and remove ".default" from the filenames of config.default.php, database.default.php, and pubsubhubbub.default.php
5. Edit each config file to reflect your server environment and personal preferences

Additional notes are available via [Google Docs](https://docs.google.com/a/shmit.com/document/d/1QfLpcVuVoN8Ky1cX7J6RrsHwse4cC8vZcPliaAqbemM/edit)
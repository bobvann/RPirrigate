# RPirrigate

RPirrigate is licensed under the Creative Commons Attribution-NonCommercial-NoDerivatives 4.0 International Public License


Raspberry Pi irrigation system software

RPirrigate provide to the users an easy-to-use, nice-to-see software for managing an irrigation system using a Raspberry Pi and few relayes and valves connected to the GPIO ports.

A guide for the hardware part of the projects still needs to be written yet.

My goal was to make it easy to install too, so I decided to use chef to build the installer

It also has full multilanguage support, with language files easy to build and add to the software.

Currently supported languages are English and Italian.

FILE STRUCTURE

/web      -> web interface

/daemon   -> daemon always running


MISCELLANEOUS NOTES:

*** WEB ***

Languages constants:

LANG_<page_name>_<element_name>

e.g. LANG_settings_NEW


**** INSTALLATION GUIDE ****
$ sudo apt-get install chef (when asking chef server just press enter, will say chef-client failed-> that's ok)

$ wget http://bobvann.noip.me/repo/rpirrigate/rpirrigate-chef.tar.gz

$ tar zxf rpirrigate-chef.tar.gz

$ cd rpirrigate-chef

$ sudo chef-solo -c solo.rb

should install, then you can login by any browser using
http://[Raspberry Pi IP]:883

Login credentials: admin/admin

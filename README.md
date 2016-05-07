# album
A simple picture gallery script for showing a series of images with no fancy functionality.

## Requirements
* Webserver like apache
* PHP5.4+ (though may work with older versions)
* Extensions gdlib and fileinfo installed

## Installation

1. Copy source to the destination on the webserver, e.g. simply with ```git clone git@github.com/eott/album```
2. Copy file ```upload/.htaccess.default``` to ```upload/.htaccess```
3. Configure file ```upload/.htaccess```, especially setting the correct absolute path to the .htpasswd file
4. If you don't want to use an existing .htpasswd create a new one under the path you entered above. See
documentation of .htpasswd files for instructions how to create a new pair of user credentials.
5. That's it, it should work now. Using the upload form you can upload images and delete them.

## Notes
* The images are sorted alphabetically and keep their names (mostly), so new images are sorted together with
existing images.
* The upload form can be very fickly concerning maximum filesizes, due to the different server configurations. If
you keep getting error messages that files are too large, you may need to increase the maximum allowed upload size.
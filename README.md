# ImgOpt
This program is an image optimization tool written in PHP. It optimizes image files within a specified directory and further outputs them in webp and avif formats. As a result, users can obtain optimized images to reduce the load time of web pages.

## webp and avif formats
- **webp :** It is an image format developed by Google, which can maintain high-quality images while having a higher compression ratio compared to existing image formats. It is primarily designed for use on the web. 
- **avif :** An abbreviation for AV1 Image File Format, it is a new image format based on the AV1 video codec. It boasts even better compression efficiency and image quality compared to webp.

## Installing
Please prepare the PHP operating environment in advance.

- UNIX-like environment (macOS, Linux, FreeBSD, Windows)
- PHP 5.6 or later

Download the [imgopt.phar](https://github.com/kaleidpixel/imgopt/releases/download/latest/imgopt.phar) file using wget or curl:

```shell
$ curl -OL https://github.com/kaleidpixel/imgopt/releases/download/latest/imgopt.phar

```

### macOS, Linux, FreeBSD

To use ImgOpt from the command line by typing `imgopt`, make the file executable and move it to somewhere in your PATH. For example:

```shell
$ chmod +x imgopt.phar
$ sudo mv imgopt.phar /usr/local/bin/imgopt

```

Alternatively, you can also install it using Homebrew. If you install via Homebrew, you can skip detailed tasks like renaming and immediately use the `imgopt` command.

```shell
$ brew tap kaleidpixel/cli
$ brew install imgopt
$ brew cleanup imgopt

```

### Windows

To run ImgOpt on a Windows environment using the `imgopt` command, additional steps are required. The imgopt.phar must be executable and placed in a location registered in the environment variable PATH. Furthermore, you'll need to create two new files: imgopt and imgopt.bat.

Using an editor capable of creating text files in UTF-8 (without BOM), please create imgopt and imgopt.bat, and save them in the same directory as imgopt.phar.

**imgopt :**
```shell
#!/bin/sh

dir=$(cd "${0%[/\\]*}" > /dev/null; pwd)

if [ -d /proc/cygdrive ]; then
    case $(which php) in
        $(readlink -n /proc/cygdrive)/*)
            dir=$(cygpath -m "$dir");
            ;;
    esac
fi

php "${dir}/imgopt.phar" "$@"

```

**imgopt.bat :**
```shell
@echo OFF
:: in case DelayedExpansion is on and a path contains ! 
setlocal DISABLEDELAYEDEXPANSION
php "%~dp0imgopt.phar" %*

```

## How to Use
The usage is quite straightforward. Just run the program by specifying the path to the directory where the images are stored using the option (-p or --path).

```shell
$ imgopt -p ./images

```
```shell
$ imgopt -p /Users/username/www/images

```

## License
* Image Optimizer: MIT License
  https://github.com/kaleidpixel/image-optimizer
* webp: BSD License  
  https://www.webmproject.org/license/software/
* gifsicle: GPL version 2 License  
  https://github.com/kohler/gifsicle/blob/master/COPYING
* jpegtran: MIT License  
  https://github.com/imagemin/jpegtran-bin/blob/master/license
* pngquant: GPL version 3 License  
  https://github.com/kornelski/pngquant/blob/master/COPYRIGHT
* svg-sanitizer: GPL version 2 License  
  https://github.com/darylldoyle/svg-sanitizer/blob/master/LICENSE
* cavif: BSD 3-Clause License  
  https://github.com/kornelski/cavif-rs/blob/main/LICENSE

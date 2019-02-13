# WPHelper &nbsp; [![build status](https://git.giftgruen.com/giftgruen/wp-helper/badges/master/build.svg)](https://git.giftgruen.com/giftgruen/wp-helper/commits/master)

#### A library to make WordPress plugins fun again :)

## Docs
[Click here](/docs.html)

## Installation
```
composer require giftgruen/wp-helper <version>
```

This project uses semver as versioning scheme. <br>
Please refer to [http://semver.org/](http://semver.org/) for more information.

## Usage
WPHelper is separated into classes to map functions to use-cases.

Example usage:
```php
<?php

use \WPHelper as WP;

WP\Common::registerPostType("TestPost", "PostID", [...]);
```

#### Don't like the `WP\`? Create your own prefix!
```php
<?php

use \WPHelper\Common as WP;

WP::registerPostType(...);
```

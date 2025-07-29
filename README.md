<p align="center">
    <a href="https://www.yiiframework.com/" target="_blank">
        <img src="https://www.yiiframework.com/files/logo/yii.png" width="400" alt="Yii Framework Website" />
    </a>
</p>

This project contains the source code for the [yiiframework.com](https://yiiframework.com/) Website.

If you want to contribute, please get in touch with us using the [issue tracker](https://github.com/yiisoft-contrib/yiiframework.com/issues).

![Build Status](https://github.com/yiisoft-contrib/yiiframework.com/actions/workflows/build.yml/badge.svg)

## Prerequisites

Install [Docker](https://docs.docker.com/get-docker/).

```
make up
```

The site will be available at http://localhost:81.

### Generating screenshots

You can use the script `run_pageres.sh` at the root of the source directory to generate screenshots.

## Initial setup

Adjust local config files if needed:

- `config/console-local.php`.
- `config/params-local.php`.
- `config/web-local.php`.

if you do not see the `*-local.php` or `yii` files, then run `./init`

Generate a personal GitHub token (from your GitHub profile settings section). Paste it in a file in the `data` directory 
(@app/data) called `github.token` (one line, no line-break). If the file does not exist, just create it ad put the token in it.

Continue with the following commands:

```sh
# run migrations
./yii migrate

# fill RBAC
./yii rbac/up

# build contributors page (this may take some time as it downloads a lot of user avatars from github)
./yii contributors/generate

# If you're on Windows you have to manually symlink or copy
# %appdata%/npm/node_modules/browser-sync to your app's node_modules

# The next step is for building the API documentation and the Guide files.
# It is optional for the site to be working but you will have no API docs and Guide.
# This step includes cloning the Yii 1 and Yii 2 repositories and a lot of computation,
# so you might want to skip it on the first install.
#
# This also requires an instance of elasticsearch to be configured and running
# (if you do not have it, it will still run, but the site search will not work).
# It also assumes you have pdflatex installed for building PDF guide docs.
#
# You may also build only parts of the docs, run  make help  for the available commands.
make docs

# If you are using Docker image, you need to additionally pass VENDOR_DIR:
make docs VENDOR_DIR=$VENDOR_DIR

# Yii 1.0 API docs generation. They are already included in VCS. Run this only if layout has changed.
docker build -f Dockerfile.yii-1.0 -t yiiframeworkcom-yii-1.0 .
docker run -it -v $PWD/data/api-1.0:/code/data/api-1.0 yiiframeworkcom-yii-1.0

# populate the search index by running
./yii search/rebuild

/code/vendor/bin/apidoc guide data/yii-2.0/docs/guide-ru data/docs-offline/yii-docs-2.0-ru --interactive=0
./yii guide "2.0" --interactive=0
```

### Data import

For importing data from the old website, the following steps are necessary:

- import data by running `./yii import` command
- rebuild user badges by running `./yii badge/rebuild` 
- calculate user ranking `./yii user/ranking`.

If you don't have that data, you can work with dummy content:

- To fill the database with dummy content, you may run the command `./yii fake-data`.
  You may run it multiple times to generate more data.
- rebuild user badges by running `./yii badge/rebuild`.
- calculate user ranking `./yii user/ranking`.

To assign users extra permissions use `./yii rbac/assign`.

### Cron jobs

The following commands need to be set up to run on a regular basis:

| command                   | interval | Purpose                                |
|---------------------------|----------|----------------------------------------|
| yii sitemap/generate      | daily    | regenerate sitemap.xml                 |
| yii contributors/generate | weekly   | update contributors list on team page  |
| yii badge/update          | hourly   | update badges for users in badge_queue |
| yii cron/update-packagist | hourly   | update packagist extension data        |
| yii user/ranking          | daily    | update user ranking                    |
| yii github-progress       | hourly   | update Github progress data            |

Additionally, `queue/listen` should run as a daemon or `queue/run` as a cronjob.

### GitHub Webhook for Automatic Version Updates

This feature allows the website to automatically update framework version information when new releases are published on GitHub.

#### Setup

##### 1. Configure Webhook Secret

Add the webhook secret to your configuration:

```php
// config/params-local.php
return [
    // ... other params
    'github-webhook-secret' => 'your-secret-key-here',
];
```

Or set via environment variable and reference it in params.

##### 2. Configure GitHub Webhooks

For each repository you want to track (e.g., `yiisoft/yii2`, `yiisoft/yii`):

1. Go to repository Settings → Webhooks
2. Click "Add webhook"
3. Configure:
   - **Payload URL**: `https://www.yiiframework.com/site/github-webhook`
   - **Content type**: `application/json`
   - **Secret**: Use the same secret as configured above
   - **Events**: Select "Releases"
   - **Active**: ✓ Checked

##### 3. Supported Repositories

Currently supported repositories:
- `yiisoft/yii2` → Updates `2.0` version series
- `yiisoft/yii` → Updates `1.1` version series

##### 4. How It Works

When a new release is published:

1. GitHub sends a webhook to `/site/github-webhook`
2. The webhook validates the signature for security
3. If the release is from a supported repository and matches the expected version format:
   - The new version is added to the top of the appropriate array in `config/versions.php`
   - The date is formatted as "Month Day, Year"

##### 5. Security

- Webhook signatures are validated using HMAC-SHA256
- Only "published" release events are processed
- Only releases from whitelisted repositories are accepted
- Only versions matching expected patterns are processed

##### 6. Testing

To test the webhook:

```bash
# Send a test payload (replace with actual secret)
curl -X POST https://www.yiiframework.com/site/github-webhook \
  -H "Content-Type: application/json" \
  -H "X-GitHub-Event: ping" \
  -d '{"zen": "Design for failure."}'
```

Expected response: "pong"

### Deployment

This section covers notes for deployment on a server, you may not need this for your dev env. OS is assumed to be Debian 
"bullseye".

```sh
apt-get install texlive-full python3-pygments git nodejs make
```

## Maintenance

The contributor list and the avatar thumbnails are generated by a console command:

```sh
./yii contributors/generate
```

It will connect to GitHub via the API and fetch a list of contributors, generate `data/contributors.json` and thumbnail
images of the user avatars in `data/avatars` and finally invoke Gulp to generate a sprite image and Sass code.

It would be a good idea to set up a Cron job to run that once in a while—perhaps once each month.

## Directory structure

      commands/           contains console commands (controllers)
      config/             contains application configurations
      controllers/        contains Web controller classes
      data/               contains important data generated by different commands
      env/                contains environment-dependent files
      assets/
          src/
              fonts/      contains fonts
              scss/       contains Sass source files
              js/         contains JS source files
      mail/               contains view files for e-mails
      models/             contains model classes
      node_modules/       contains installed NPM packages
      runtime/            contains files generated during runtime
      scripts/            contains shell scripts
      vendor/             contains dependent 3rd-party packages
      views/              contains view files for the Web application
      web/                contains the entry script and Web resources


## Development

### Build

* During development, run `gulp` to watch view, Sass and JS file changes and automatically build target CSS/JS files. 
  This command will also launch a browser window which is connected to browsersync.
* At any time, run `gulp build` to manually rebuild target CSS/JS files from source Sass/JS files.
* If you only want to watch for changes, you can issue the command `gulp watch`
* To build the assets for production, specify the `production` flag: `gulp build --production` or run `npm run build`

### CSS Files

* Use Sass files to define CSS styles.
* All Sass files should be put under `assets/src/scss` and listed in `assets/src/scss/all.scss`.
* Usually each controller corresponds to a single Sass file whose name is the same as the controller ID.
  For example, the `GuideController` has a Sass file named `_guide.scss`.
* All Sass source files, except `all.scss` should have a leading underscore in the name. Sass will ignore files starting with an underscore so that only one CSS file will be produced (all.css).
* For information about where each file should be put, please consult the master include file `all.scss`.

### JS Files

* All JS files should be put under `assets/src/js` and listed in `config.yml`.
* Usually each controller corresponds to a single JS file whose name is the same as the controller ID.
  For example, the `GuideController` has a JS file named `guide.js`.

## Links

* [Gulp](https://gulpjs.com/)
* [Browsersync](https://www.browsersync.io/)
* [Sass](https://sass-lang.com/)

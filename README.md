GitHub WebHooks Handler
==========================

## Overview ##

This is a simple package that handle GitHub WebHooks calls.

## Install ##

1. Download this package 
2. Copy `config/config.yml.dist` to `config/config.yml` and insert your params
3. Run `composer install`
4. Configure your GitHub repository webhook to be called every time commits are
   pushed to GitHub

### GitHub repository configuration ###

To set up a repository webhook on GitHub, head over to the **Settings** page of your
repository, and click on **Webhooks & services**. After that, click on **Add webhook**.

Fill in following values in form:
* **Payload URL** - Enter full URL to your webhook script
* **Content type** - should be "application/x-www-form-urlencoded"

Click **Add webhook** button and that's it.

### Test locally ###

To test the webhook on your local machine you could use something like [ngrok](https://ngrok.com/).

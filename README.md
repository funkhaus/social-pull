# social-pull
Wordpress plugin to easily import information from one or many social media accounts.

### How

1. Drop the contents of this repo into `wp-content/plugins/social-pull` and activate the plugin via `Plugins -> Installed Plugins`.
1. Go to `Tools -> Social Pull Options` and enter a random ASCII string to serve as your custom secure token. Hit `Save Changes` to update your webhook.
1. Use a service like [Zapier](https://zapier.com/) to fire the given webhook each time your desired social media account is active. **For example:** In Zapier, select `User Tweet` as the trigger and `Webhooks by Zapier` as the action. Enter the Twitter username of the desired user in User Tweet options. POST to the webhook provided by `Social Pull Options`. This will create a new Social post each time the desired user creates a public tweet.

That's it! You'll be able to see a new post type, Social Posts, in your Wordpress dashboard, and you'll be able to run queries for Social Posts and their data just like you could for any other post type.

-------

Version 1.0

http://funkhaus.us

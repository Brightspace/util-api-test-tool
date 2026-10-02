# API-Test-Tool


A tool for testing Valence API calls.



## Use it online

WARNING: This tool must be hosted in an isolated VPN to mitigate a known SSRF vulnerability

D2L hosts a copy of this tool at [apitesttool.desire2learnvalence.com](https://apitesttool.desire2learnvalence.com/)

## Usage Instructions

1. Log into your test LMS as a user with permission to register ID Key Authorization apps, i.e., your role must have the permission `Manage Extensibility` > `Can Manage API Applications` ([Community reference article](https://community.d2l.com/brightspace/kb/articles/27522-manage-extensibility-permissions)).

2. Admin Cog > Manage Extensibility > ID Key Authorization. Click **Register an App**.

3. Give your app an intuitive name, and for `Trusted URL`, supply `https://apitesttool.desire2learnvalence.com/` - including the trailing slash.

4. Keep the **Enable this application** box checked. Check the box for **I accept the API Developer Agreemement** and click **Register Application**.

5. The app will register and you will be presented with an Application ID and an Application Key.

6. In the same browser session, open another tab and navigate to `https://apitesttool.desire2learnvalence.com/`.  Enter values as follows:
* Host: hostname for your test LMS site, e.g., `yourtestsite.desire2learn.com`
* Port: `443`
* HTTPS: *check the box*
* App ID: *copy and paste the Application ID value from your registered app*
* App Key: *copy and paste the Application Key value from your registered app - click "Show" before copying*

7. **Important**: Before authenticating, enter a value into the `Profile Name` input and click **Save**.  You should see a brief alert "Success! Profile has now been saved."

8. Click **Authenticate**.  You'll be redirected to your test LMS' consent page - "Application [your application name] is trying to access your information...". Since you've already logged into the LMS in the first tab in your session, you won't have to log in again. Check the box and click **Continue**.

9. You'll be returned to the API test tool page. The Host, Port, App ID, and App Key fields should remain populated with the values you supplied, and will be disabled from editing at this point. The User ID and User Key fields should also be populated and disabled from editing.

10. Finally, test your application: Click the **WhoAmI** button in the examples section to pre-populate a `GET` request with the WhoAmI API route.  Click **Submit**.
* `Status` should show `Success (HTTP status 200)`.
* `Response` should be a JSON object with the details of the LMS user you logged in with and registered the app.

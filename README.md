# Sherpa
A simple tool to marry a stored template with a stored list and schedule a delivery.

This is a simple tool that will:
 - connect to your SparkPost account
 - list all your stored recipients lists
 - allow you to select one to send to
 - list all your stored templates
 - allow you to select one to use
 - schedule a job to send the tempalte to the list

## Setup
Clone this repo, then modify env.php with your API key

Then point your browser to https://<YourWebServerLocation>/sherpa

## WARNINGS
 - Setting your API key without also adding some layer of authentication will leave your lists exposed.  Please use this behind a VPN/firewall, or add some login capability.  Alternately, set your API key on each use instead of using the ENV file.

 - Scheduling can only occur up to 3 days in advance.  If you schedule a job for more than 3 days in the future, it will throw an error.

 - The only way to cancel a scheduled transmission is with a campaign ID, so remember ot use one.

 - Make sure you chmod 766 campaigns.txt because the code need to write to it.



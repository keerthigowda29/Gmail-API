# Gmail-API
Pre-requisites:
1. PHP 5.4 or greater with the command-line interface (CLI) and JSON extension installed
2. The Composer dependency management tool
3. A Google Cloud Platform project with the API enabled. 
4. To create a project and enable an API by following instructions in this link https://developers.google.com/workspace/guides/create-project
5. While creating the project, specify the redirect URI of your choice which will be used to start the application.
6. While creating credentials, give the access to your mail id which you will be using to test.
7.Download the credentails file and rename to credentials.json and copy it to your working directory.



Steps:
1. Execute the following command "composer require google/apiclient:^2.0" in your working directory
2. Run the following command "php -S localhost:9000" (localhost:9000 is the redirect URI from pre-requisites step 5)

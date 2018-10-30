
## php cloudflare-api-V4

php binding for cloudflare api v4



### examples

```php
$api = new cloudflare_api('your_email_here@yoursite.com','your_api_key_here');


//get_zones
$result = $api->get_zones();

//get dev mode
$result = $api->dev_mode($identifier);

//dns_records
$identifier = $api->identifier('yoursite.com');
$result = $api->dns_records($identifier);

//get_dns_record
$result = $api->get_dns_record($identifier,'A','yoursite.com');

//create_dns_record | eg A record for ftp.yoursite.com to point to 127.0.0.1
$identifier = $api->identifier('yoursite.com');
$result = $api->create_dns_record($identifier,'A','ftp','127.0.0.1');

//get_dns_record_id
$dns_record_id = $api->get_dns_record_id($identifier,'A','ftp.yoursite.com');

//update_dns_record
$identifier = $api->identifier('yoursite.com');
$dns_record_id = $api->get_dns_record_id($identifier,'A','ftp.yoursite.com');
$response = $api->update_dns_record($identifier,$dns_record_id,'A','ftp','127.0.1.1');

//delete_dns_record
$identifier = $api->identifier('yoursite.com');
$dns_record_id = $api->get_dns_record_id($identifier,'A','ftp.yoursite.com');
$response = $api->delete_dns_record($identifier,$dns_record_id);

//purge_site_cache
$identifier = $api->identifier('yoursite.com');
$result = $api->purge_site($identifier);

//purge_files_cache
$identifier = $api->identifier('yoursite.com');
$files = [
	'http://yoursite.com/skin.css',
    'http://yoursite.com/skin.js'
];
$result = $api->purge_files($identifier,$files);

//analytics
$analytics = $api->analytics($identifier,-10080, 0);

/**
* User Section
*/
//get_user_details 
$user_details = $api->get_user_details();

//update_user_details
$response = $api->update_user_details("first_name","last_name","telephone","country","zipcode");

```

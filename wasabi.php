<?php
	ini_set('display_startup_errors', 1);
	ini_set('display_errors', 1);
	error_reporting(-1);

	// Include the SDK using the Composer autoloader
	require 'vendor/autoload.php';

	use Aws\S3\S3Client;

	$key = "15AXQMANCB2TOXJ3H836";
	$secret = "fmXrs7lGcjAwlvTWGC2AYjesCoNfAi2WEh5lciOT";

	// Instantiate the S3 client using your Wasabi profile
	$s3Client = S3Client::factory(array(
		'endpoint' => 'http://s3.wasabisys.com',
		//'profile' => 'wasabi',
		'region' => 'eu-central-1',
		'version' => 'latest',
		'credentials' => [
            'key' => $key,
            'secret' => $secret,
    	]
	));

	//Sample to create a bucket
	$s3Client->createBucket(array('Bucket' => 'tdfst124734kdf'));
?>
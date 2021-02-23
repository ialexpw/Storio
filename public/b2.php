<?php
	ini_set('display_startup_errors', 1);
	ini_set('display_errors', 1);
	error_reporting(-1);

	// Include the SDK using the Composer autoloader
	require 'vendor/autoload.php';

	use obregonco\B2\Client;
	use obregonco\B2\Bucket;

	$client = new Client('accountId', [
		'keyId' => '003b2d1d93495cc0000000001', // optional if you want to use master key (account Id)
		'applicationKey' => 'K003IuBRkGDbE50tEnCArCNcGwNeMN4',
	]);

	$client->version = 2; // By default will use version 1
	//$client->domainAliases = [ // When you want to use your own domains (using CNAME)
	//	'f0001.backblazeb2.com' => 'alias01.mydomain.com',
	//];

	$client->largeFileLimit = 3000000000; // Lower limit for using large files upload support. Default: 3GB

	// Returns a Bucket object.
	//$bucket = $client->createBucket([
	//	'BucketName' => 'my-special-bucket',
	//	'BucketType' => Bucket::TYPE_PRIVATE // or TYPE_PUBLIC
	//]);

	// Change the bucket to private. Also returns a Bucket object.
	//$updatedBucket = $client->updateBucket([
	//	'BucketId' => $bucket->getId(),
	//	'BucketType' => Bucket::TYPE_PUBLIC
	//]);

	// Retrieve an array of Bucket objects on your account.
	$buckets = $client->listBuckets();

	print_r($buckets);

	// Delete a bucket.
	//$client->deleteBucket([
	//	'BucketId' => '4c2b957661da9c825f465e1b'
	//]);

	// Upload a file to a bucket. Returns a File object.
	//$file = $client->upload([
	//	'BucketName' => 'my-special-bucket',
	//	'FileName' => 'path/to/upload/to',
	//	'Body' => 'I am the file content'

		// The file content can also be provided via a resource.
		// 'Body' => fopen('/path/to/input', 'r')
	//]);

	// Download a file from a bucket. Returns the file content.
	//$fileContent = $client->download([
	//	'FileId' => $file->getId()

		// Can also identify the file via bucket and path:
		// 'BucketName' => 'my-special-bucket',
		// 'FileName' => 'path/to/file'

		// Can also save directly to a location on disk. This will cause download() to not return file content.
		// 'SaveAs' => '/path/to/save/location'
	//]);

	// Delete a file from a bucket. Returns true or false.
	//$fileDelete = $client->deleteFile([
	//	'FileId' => $file->getId()
		
		// Can also identify the file via bucket and path:
		// 'BucketName' => 'my-special-bucket',
		// 'FileName' => 'path/to/file'
	//]);

	// Retrieve an array of file objects from a bucket.
	//$fileList = $client->listFiles([
	//	'BucketId' => '4d2dbbe08e1e983c5e6f0d12'
	//]);
?>
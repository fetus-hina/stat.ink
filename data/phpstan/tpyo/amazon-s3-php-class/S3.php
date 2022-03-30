<?php

class S3
{
    const ACL_PRIVATE = 'private';
    const ACL_PUBLIC_READ = 'public-read';
    const ACL_PUBLIC_READ_WRITE = 'public-read-write';
    const ACL_AUTHENTICATED_READ = 'authenticated-read';

    const STORAGE_CLASS_STANDARD = 'STANDARD';
    const STORAGE_CLASS_RRS = 'REDUCED_REDUNDANCY';

    const SSE_NONE = '';
    const SSE_AES256 = 'AES256';

	/**
	* Put an object
	*
	* @param mixed $input Input data
	* @param string $bucket
	* @param string $uri
    * @param self::ACL_PRIVATE|self::ACL_PUBLIC_READ|self::ACL_PUBLIC_READ_WRITE|self::ACL_AUTHENTICATED_READ $acl
	* @param array<string, string> $metaHeaders
	* @param array<string, string> $requestHeaders
	* @param self::STORAGE_CLASS_STANDARD|self::STORAGE_CLASS_RRS $storageClass
	* @param self::SSE_NONE|self::SSE_AES256 $serverSideEncryption
	* @return bool
	*/
	public static function putObject(
        $input,
        $bucket,
        $uri,
        $acl = self::ACL_PRIVATE,
        $metaHeaders = array(),
        $requestHeaders = array(),
        $storageClass = self::STORAGE_CLASS_STANDARD,
        $serverSideEncryption = self::SSE_NONE
    ) {
    }
}

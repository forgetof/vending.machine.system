<?php

namespace common\components;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

use Aws\S3\S3Client;
use Aws\Credentials\Credentials;


class AwsS3
{
    public $credentials;
    public $region;
    public $bucket;
    public $defaultAcl;

    public function upload($key, $data, $acl, $params)
    {
        $s3 = new S3Client([
            'region'      => $this->region,
            'version'     => 'latest',
            'credentials' => $this->credentials,
        ]);

        $result = $s3->putObject([
            'Bucket'       => $this->bucket,
            'Key'          => $key,
            'Body'         => $data,
            'ACL'          => $this->defaultAcl,
            'CacheControl' => 'max-age=31536000',
        ]);

        return $result;
    }

    public function delete($key)
    {
        $s3 = new S3Client([
            'region'      => $this->region,
            'version'     => 'latest',
            'credentials' => $this->credentials,
        ]);
        
        $result = $s3->deleteObject([
            'Bucket' => $this->bucket,
            'Key' => $key,
        ]);

        return $result;
    }
}

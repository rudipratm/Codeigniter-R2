<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once FCPATH . '/vendor/autoload.php';

use Aws\S3\S3Client;

class R2 {
    protected $CI;
    protected $s3;

    public function __construct() {
        $this->CI =& get_instance();
        $this->CI->load->config('r2');

        $this->s3 = new S3Client([
            'version'     => 'latest',
            'region'      => $this->CI->config->item('region', 'r2'),
            'endpoint'    => $this->CI->config->item('endpoint', 'r2'),
            'credentials' => [
                'key'    => $this->CI->config->item('key', 'r2'),
                'secret' => $this->CI->config->item('secret', 'r2'),
            ],
        ]);
    }

    public function upload($filePath, $fileName) {
        try {
            $result = $this->s3->putObject([
                'Bucket' => $this->CI->config->item('bucket', 'r2'),
                'Key'    => $fileName,
                'SourceFile' => $filePath,
                'ACL'    => 'public-read',
            ]);
            return $result->get('ObjectURL');
        } catch (AwsException $e) {
            return $e->getMessage();
        }
    }

    public function getUrl($fileName) {
        return $this->s3->getObjectUrl(
            $this->CI->config->item('bucket', 'r2'),
            $fileName
        );
    }
}

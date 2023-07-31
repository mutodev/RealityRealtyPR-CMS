<?php

use Aws\S3\S3Client;
use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local as LocalAdapter;
use League\Flysystem\Adapter\AwsS3 as AwsS3Adapter;

if (isset($_ENV['AMAZON_KEY']) && isset($_ENV['AMAZON_SECRET'])) {

    $client = S3Client::factory(array(
        'key'    => $_ENV['AMAZON_KEY'],
        'secret' => $_ENV['AMAZON_SECRET'],
    ));

    $adapter = new AwsS3Adapter($client, 'app-propiedades');
}
else {
    $adapter = new LocalAdapter(APPLICATION_PATH);
}

Configure::write("filesystem", new Filesystem($adapter));

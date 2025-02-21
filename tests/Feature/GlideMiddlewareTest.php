<?php

it('denies access when the signature is missing', function () {
    $response = $this->get('glide/v1/test.jpg');
    $response->assertStatus(403);

    $response = $this->get('glide/v2/test.jpg/W10.jpg');
    $response->assertStatus(403);
});

it('denies access when the signature is invalid', function () {
    $response = $this->get('glide/v1/test.jpg?s=somethingobviouslyinvalid');
    $response->assertStatus(403);

    $response = $this->get('glide/v2/test.jpg/W10.jpg?s=somethingobviouslyinvalid');
    $response->assertStatus(403);
});

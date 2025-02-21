<?php

it('redirects properly', function () {
    $response = $this->get('glide/v1/test.jpg?w=50&s=1ab98097c09d4f7e6fb2c6186f46f2f3');
    $response->assertRedirect('http://localhost/glide/v2/dGVzdC5qcGc/eyJ3IjoiNTAifQ.jpg?s=871e28eb41d7ff469dee41682ddba89e');

    $response = $this->get('glide/v1/test.jpg?s=24bbecd5a2413de7bde6bd29ae8520e1');
    $response->assertRedirect('test.jpg');
});

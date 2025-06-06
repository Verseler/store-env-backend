<?php

test('example', function () {
    $response = $this->get('/');

    $response->assertStatus(404); //404: because there is no "/" end point implemented
});

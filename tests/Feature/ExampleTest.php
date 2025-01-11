<?php

it('returns a successful response', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
});

it('api returns a successful response', function () {
    $response = $this->get('/api');

    $response->assertStatus(200);
});

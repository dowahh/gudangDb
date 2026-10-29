<?php

test('halaman utama mengarahkan ke halaman lain', function () {
    $response = $this->get('/');

    $response->assertRedirect();
});

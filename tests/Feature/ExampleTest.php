<?php

it('home redireciona para rota inicial', function () {
    $response = $this->get('/');
    $response->assertStatus(302);
});
